<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentController extends Controller
{
    /* ──────────────────────────────────────────────────────────
       Customer pages
       ────────────────────────────────────────────────────────── */

    /**
     * Printable invoice — customers see their own, staff may print any.
     */
    public function show(Request $request, string $invoice)
    {
        $user = Auth::user();

        $query = Invoice::with(['shipment', 'user'])->where('id', $invoice);
        if (! $user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        return view('client.invoices.show', ['invoice' => $query->firstOrFail()]);
    }

    /**
     * Choose how to pay (mobile money / card), with bank transfer as the
     * WhatsApp fallback.
     */
    public function checkout(Request $request, string $invoice)
    {
        $invoice = Invoice::where('id', $invoice)->where('user_id', Auth::id())->firstOrFail();

        if ($invoice->status === 'Paid') {
            return redirect()->route('client.invoices.show', $invoice)
                ->with('info', 'This invoice is already paid.');
        }

        if (! $invoice->canPayOnline()) {
            return redirect()->to(route('client.profile').'#invoices')
                ->with('error', 'Online payment isn’t available for this invoice — use “How to pay” next to it and we’ll send you the details.');
        }

        return view('client.payments.checkout', ['invoice' => $invoice]);
    }

    /**
     * Start the collection at Lipila, then send the customer to the card
     * page or to our "check your phone" result page.
     */
    public function pay(Request $request, string $invoice)
    {
        $invoice = Invoice::where('id', $invoice)->where('user_id', Auth::id())->firstOrFail();

        if ($invoice->status === 'Paid') {
            return redirect()->route('client.invoices.show', $invoice)
                ->with('info', 'This invoice is already paid.');
        }

        if (! $invoice->canPayOnline()) {
            return redirect()->to(route('client.profile').'#invoices')
                ->with('error', 'Online payment isn’t available for this invoice.');
        }

        $validated = $request->validate([
            'method' => 'required|in:card,momo',
            'phone' => 'required_if:method,momo|nullable|string|max:20',
        ]);

        // The reference is ours: it goes to Lipila and comes back in the
        // callback/status responses, which is how we match payments.
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'reference_id' => (string) Str::uuid(),
            'method' => $validated['method'],
            'account_number' => $validated['method'] === 'momo'
                ? $this->normalizeMsisdn($validated['phone'])
                : preg_replace('/[^\d+]/', '', (string) Auth::user()->phone),
            'amount' => $this->chargeAmount($invoice),
            'currency' => $invoice->currency,
            'status' => 'pending',
        ]);

        try {
            $response = $validated['method'] === 'card'
                ? $this->initiateCard($invoice, $payment)
                : $this->initiateMobileMoney($invoice, $payment, $validated['phone']);
        } catch (\Throwable $e) {
            Log::warning('Lipila collection could not be started', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            $payment->update(['status' => 'failed', 'message' => 'Gateway unreachable']);

            return redirect()->route('client.payments.result', $payment)
                ->with('error', 'We couldn’t reach the payment gateway. Please try again in a moment.');
        }

        $data = $response->json() ?? [];

        if ($response->failed()) {
            $message = $data['message'] ?? 'The payment gateway rejected the request (HTTP '.$response->status().').';
            $payment->update(['status' => 'failed', 'message' => $message]);

            return redirect()->route('client.payments.result', $payment)->with('error', $message);
        }

        if (($data['status'] ?? null) === 'Failed') {
            $payment->update(['status' => 'failed', 'message' => $data['message'] ?? null]);

            return redirect()->route('client.payments.result', $payment)
                ->with('error', $data['message'] ?? 'The payment could not be started.');
        }

        $payment->update([
            'identifier' => $data['identifier'] ?? $payment->identifier,
            'payment_type' => $data['paymentType'] ?? $payment->payment_type,
            'message' => $data['message'] ?? $payment->message,
        ]);

        // Fast (usually sandbox) payments can already be settled here.
        if (($data['status'] ?? null) === 'Successful') {
            $this->settle($payment, 'Successful', $data);
        }

        if ($validated['method'] === 'card' && ! empty($data['cardRedirectionUrl'])) {
            return redirect()->away($data['cardRedirectionUrl']);
        }

        return redirect()->route('client.payments.result', $payment);
    }

    /**
     * Waiting/result page: shows the payment and polls its status.
     */
    public function result(Request $request, Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(404);
        }

        return view('client.payments.result', ['payment' => $payment->load('invoice')]);
    }

    /**
     * Polled by the result page. Checks Lipila, settles locally when the
     * transaction is final, and stays "pending" while it isn't.
     */
    public function status(Request $request, Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(404);
        }

        if ($payment->status !== 'pending') {
            return response()->json(['status' => $payment->status]);
        }

        try {
            $response = $this->lipilaRequest('GET', '/api/v1/collections/check-status', [
                'referenceId' => $payment->reference_id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Lipila status check failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);

            return response()->json(['status' => 'pending']);
        }

        if ($response->failed()) {
            // Not known (yet) or a transient error — keep the page polling.
            return response()->json(['status' => 'pending']);
        }

        $data = $response->json() ?? [];
        if (in_array($data['status'] ?? '', ['Successful', 'Failed'], true)) {
            $this->settle($payment, $data['status'], $data);
        }

        return response()->json(['status' => $payment->fresh()->status]);
    }

    /**
     * Lipila callback. Public route — authenticity comes from the HMAC
     * signature over the raw body (Standard Webhooks scheme), never from
     * the payload alone.
     */
    public function webhook(Request $request)
    {
        if (! $this->webhookSignatureIsValid($request)) {
            Log::warning('Lipila webhook rejected: signature/timestamp invalid');

            return response()->json(['error' => 'invalid signature'], 401);
        }

        $data = $request->json()->all();
        $referenceId = $data['referenceId'] ?? null;

        if (! $referenceId) {
            return response()->json(['ok' => true]);
        }

        $payment = Payment::where('reference_id', $referenceId)->first();

        if (! $payment) {
            // Acknowledge so Lipila stops retrying; local polling reconciles
            // anything that raced ahead of our database write.
            Log::info('Lipila webhook for unknown reference', ['reference_id' => $referenceId]);

            return response()->json(['ok' => true]);
        }

        $webhookId = $request->header('webhook-id');
        if ($webhookId && $payment->webhook_id === $webhookId) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        // Only collection results affect invoices — ignore anything else.
        if (($data['type'] ?? 'Collection') === 'Collection'
            && in_array($data['status'] ?? '', ['Successful', 'Failed'], true)) {
            $this->settle($payment, $data['status'], $data);

            if ($webhookId) {
                $payment->update(['webhook_id' => $webhookId]);
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Staff confirmation for bank transfers (no gateway involved).
     */
    public function markPaid(Request $request, string $invoice)
    {
        if (! Auth::user()->isStaff()) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($invoice);

        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('info', "Invoice {$invoice->invoice_number} is already marked as paid.");
        }

        $invoice->update(['status' => 'Paid', 'paid_at' => now()]);

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_number} marked as paid.");
    }

    /* ──────────────────────────────────────────────────────────
       Lipila API
       ────────────────────────────────────────────────────────── */

    private function initiateCard(Invoice $invoice, Payment $payment): ClientResponse
    {
        $user = Auth::user();
        [$first, $last] = $this->splitName($user->name);
        $phone = preg_replace('/[^\d+]/', '', (string) $user->phone);

        return $this->lipilaRequest('POST', '/api/v1/collections/card', [
            'customerInfo' => [
                'firstName' => $first,
                'lastName' => $last,
                'phoneNumber' => $phone ?: '000000000',
                // Lipila requires these; the portal only keeps a free-text
                // address, so city/zip are placeholders for now.
                'city' => 'N/A',
                'country' => $this->countryFromPhone($phone),
                'address' => $user->address ?: 'N/A',
                'zip' => '00000',
                'email' => $user->email,
            ],
            'collectionRequest' => [
                'referenceId' => $payment->reference_id,
                'amount' => $this->chargeAmount($invoice),
                'narration' => $this->narration($invoice),
                'accountNumber' => $phone ?: '000000000',
                'currency' => $invoice->currency,
                'backUrl' => route('client.payments.result', $payment),
                'referenceData' => $this->narration($invoice),
            ],
        ], ['callbackUrl' => route('payments.webhook')]);
    }

    private function initiateMobileMoney(Invoice $invoice, Payment $payment, string $phone): ClientResponse
    {
        $payload = [
            'referenceId' => $payment->reference_id,
            'amount' => $this->chargeAmount($invoice),
            'narration' => $this->narration($invoice),
            'accountNumber' => $this->normalizeMsisdn($phone),
            'currency' => $invoice->currency,
            'referenceData' => $this->narration($invoice),
        ];

        if ($invoice->user && $invoice->user->email) {
            $payload['email'] = $invoice->user->email;
        }

        return $this->lipilaRequest('POST', '/api/v1/collections/mobile-money', $payload, [
            'callbackUrl' => route('payments.webhook'),
        ]);
    }

    private function lipilaRequest(string $method, string $path, array $payload = [], array $headers = []): ClientResponse
    {
        $key = config('services.lipila.key');

        if (! filled($key)) {
            throw new RuntimeException('Lipila API key is not configured.');
        }

        $url = rtrim(config('services.lipila.base_url'), '/').$path;

        $client = Http::withHeaders($headers + [
            'accept' => 'application/json',
            'x-api-key' => $key,
        ])->timeout(25);

        return $method === 'GET'
            ? $client->get($url, $payload)
            : $client->post($url, $payload);
    }

    /* ──────────────────────────────────────────────────────────
       Settling & verification
       ────────────────────────────────────────────────────────── */

    /**
     * Apply a final gateway status to the payment (and the invoice when it
     * succeeds). Idempotent: a successful payment never regresses, so
     * callback + poll + redirect can all race safely.
     */
    private function settle(Payment $payment, string $gatewayStatus, array $payload = []): void
    {
        if ($payment->status === 'successful') {
            return;
        }

        $payment->fill([
            'status' => $gatewayStatus === 'Successful' ? 'successful' : 'failed',
            'payment_type' => $payload['paymentType'] ?? $payment->payment_type,
            'external_id' => $payload['externalId'] ?? $payment->external_id,
            'identifier' => $payload['identifier'] ?? $payment->identifier,
            'message' => $payload['message'] ?? $payment->message,
        ])->save();

        if ($payment->status === 'successful') {
            $invoice = $payment->invoice;

            if ($invoice && $invoice->status !== 'Paid') {
                $invoice->update(['status' => 'Paid', 'paid_at' => now()]);
            }
        }
    }

    /**
     * Standard Webhooks signature: HMAC-SHA256 over
     * "{webhook-id}.{webhook-timestamp}.{raw_body}" with the base64 secret,
     * 5-minute timestamp tolerance, constant-time comparison of every
     * space-delimited signature (key rotation sends more than one).
     */
    private function webhookSignatureIsValid(Request $request): bool
    {
        $secret = config('services.lipila.webhook_secret');
        $webhookId = $request->header('webhook-id');
        $timestamp = $request->header('webhook-timestamp');
        $signature = $request->header('webhook-signature');

        if (! filled($secret) || ! $webhookId || ! $timestamp || ! $signature) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $key = base64_decode($secret);
        $signedPayload = $webhookId.'.'.$timestamp.'.'.$request->getContent();
        $expected = 'v1,'.base64_encode(hash_hmac('sha256', $signedPayload, $key, true));

        foreach (explode(' ', $signature) as $candidate) {
            if (hash_equals($expected, trim($candidate))) {
                return true;
            }
        }

        return false;
    }

    /* ──────────────────────────────────────────────────────────
       Small helpers
       ────────────────────────────────────────────────────────── */

    private function chargeAmount(Invoice $invoice): float
    {
        return round((float) $invoice->amount, 2);
    }

    private function narration(Invoice $invoice): string
    {
        return 'Forus Freight invoice '.$invoice->invoice_number;
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: ['Customer'];

        return [$parts[0], $parts[1] ?? ''];
    }

    /**
     * Zambian-style local numbers (0…) become 260…; everything else is sent
     * as digits only. Lipila expects the international form.
     */
    private function normalizeMsisdn(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = '260'.substr($digits, 1);
        }

        return $digits;
    }

    private function countryFromPhone(string $phone): string
    {
        $map = [
            '260' => 'ZM', '27' => 'ZA', '263' => 'ZW', '255' => 'TZ', '243' => 'CD',
            '265' => 'MW', '267' => 'BW', '264' => 'NA', '268' => 'SZ', '266' => 'LS', '258' => 'MZ',
        ];

        foreach ($map as $prefix => $country) {
            if (str_starts_with($phone, '+'.$prefix) || str_starts_with($phone, $prefix)) {
                return $country;
            }
        }

        return 'ZM';
    }
}
