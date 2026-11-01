<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_payment_webhook_is_rejected(): void
    {
        config(['services.lipila.webhook_secret' => base64_encode('test-secret')]);

        $response = $this->call('POST', route('payments.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_WEBHOOK_ID' => 'wh-invalid',
            'HTTP_WEBHOOK_TIMESTAMP' => (string) time(),
            'HTTP_WEBHOOK_SIGNATURE' => 'v1,invalid',
        ], json_encode(['referenceId' => 'missing']));

        $this->assertSame(401, $response->getStatusCode());
    }

    public function test_valid_successful_payment_webhook_settles_invoice(): void
    {
        config(['services.lipila.webhook_secret' => base64_encode('test-secret')]);
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-WEBHOOK-001',
            'amount' => 150,
            'currency' => 'ZMW',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'reference_id' => 'ref-webhook-001',
            'method' => 'momo',
            'amount' => 150,
            'currency' => 'ZMW',
            'status' => 'pending',
        ]);

        $payload = json_encode(['referenceId' => $payment->reference_id, 'status' => 'Successful']);
        $id = 'wh-valid';
        $timestamp = (string) time();
        $signed = $id.'.'.$timestamp.'.'.$payload;
        $signature = 'v1,'.base64_encode(hash_hmac('sha256', $signed, 'test-secret', true));

        $this->call('POST', route('payments.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_WEBHOOK_ID' => $id,
            'HTTP_WEBHOOK_TIMESTAMP' => $timestamp,
            'HTTP_WEBHOOK_SIGNATURE' => $signature,
        ], $payload)->assertOk();

        $this->assertSame('successful', $payment->fresh()->status);
        $this->assertSame('Paid', $invoice->fresh()->status);
    }
}
