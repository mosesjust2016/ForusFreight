<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWhatsappCampaignMessage;
use App\Services\SmsService;
use App\Services\GreenApiService;
use App\Models\User;
use App\Models\Company;
use App\Models\CommunicationsLog;
use App\Models\CommunicationOptOut;
use App\Models\MessageTemplate;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignRecipient;

class BulkCommunicationsController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /* ==================== SMS ==================== */

    public function sms(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = User::where('is_admin', false);
        if ($request->has('filter')) {
            match ($request->filter) {
                'leads'      => $query->where('crm_status', 'lead'),
                'active'     => $query->where('crm_status', 'active'),
                'high_value' => $query->where('crm_status', 'high_value'),
                'company'    => $query->whereNotNull('company_id'),
                default      => $query,
            };
        }

        $contacts = $query->whereNotNull('phone')->latest()->get();
        $companies = Company::all();
        $stats = $this->contactStats();

        return view('admin.crm.communications.sms', compact('contacts', 'companies', 'stats'));
    }

    public function sendSms(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'recipients'   => 'nullable|array',
            'recipients.*' => 'nullable|string',
            'bulk_numbers' => 'nullable|string',
            'message'      => 'required|string|max:640',
        ]);

        // Collect all recipients
        $allPhones = [];
        if (!empty($validated['recipients'])) {
            foreach ($validated['recipients'] as $phone) {
                $allPhones[] = $this->normalizePhone($phone);
            }
        }
        if (!empty($validated['bulk_numbers'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['bulk_numbers']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $phone = $line;
                if (str_contains($line, ',')) $phone = trim(explode(',', $line, 2)[1] ?? $line);
                if (str_contains($line, '|')) $phone = trim(explode('|', $line, 2)[1] ?? $line);
                $phone = $this->normalizePhone($phone);
                if ($phone) $allPhones[] = $phone;
            }
        }
        $allPhones = array_values(array_unique(array_filter($allPhones)));

        if (count($allPhones) === 0) {
            return back()->with('error', 'No valid recipients found. Select contacts or paste numbers.');
        }

        $smsService = app(SmsService::class);
        $results = ['sent' => 0, 'failed' => 0, 'total' => count($allPhones), 'errors' => []];

        foreach ($allPhones as $phone) {
            if (CommunicationOptOut::where('phone', $phone)->where('channel', 'sms')->exists()) {
                $results['failed']++;
                $results['errors'][] = ['phone' => $phone, 'reason' => 'Recipient opted out.'];
                continue;
            }

            $ok = $smsService->send($phone, $validated['message']);
            CommunicationsLog::create([
                'user_id' => Auth::id(), 'channel' => 'sms', 'direction' => 'outgoing',
                'recipient_phone' => $phone, 'message' => $validated['message'],
                'status' => $ok ? 'sent' : 'failed',
            ]);

            $ok ? $results['sent']++ : ($results['failed']++ && $results['errors'][] = ['phone' => $phone, 'reason' => 'Molo API error.']);
        }

        $status = $results['failed'] === 0 ? 'success' : ($results['sent'] > 0 ? 'warning' : 'error');
        $message = match($status) {
            'success' => "Bulk SMS sent successfully! {$results['sent']} / {$results['total']} delivered.",
            'warning' => "Partially sent. {$results['sent']} delivered, {$results['failed']} failed.",
            default   => "Bulk SMS failed. {$results['failed']} / {$results['total']} could not be sent.",
        };
        return back()->with($status, $message)->with('bulk_results', $results);
    }

    /* ==================== WHATSAPP ==================== */

    public function whatsapp(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = User::where('is_admin', false);
        if ($request->has('filter')) {
            match ($request->filter) {
                'leads'      => $query->where('crm_status', 'lead'),
                'active'     => $query->where('crm_status', 'active'),
                'high_value' => $query->where('crm_status', 'high_value'),
                'company'    => $query->whereNotNull('company_id'),
                default      => $query,
            };
        }

        $contacts = $query->whereNotNull('phone')->latest()->get();
        $companies = Company::all();
        $stats = $this->contactStats();

        $greenApi = app(GreenApiService::class);
        $waState = $greenApi->getState();

        // Warm-up
        $firstAuthDate = cache('wa_first_auth_date');
        $warmupDays = $firstAuthDate ? now()->diffInDays(\Carbon\Carbon::parse($firstAuthDate)) : 0;
        $isWarmingUp = $warmupDays < 10;

        // Response ratio (7d)
        $outgoing = CommunicationsLog::whatsapp()->outgoing()->where('created_at', '>=', now()->subDays(7))->count();
        $incoming = CommunicationsLog::whatsapp()->incoming()->where('created_at', '>=', now()->subDays(7))->count();
        $responseRatio = $outgoing > 0 ? round(($incoming / $outgoing) * 100, 1) : 0;

        // Queue status
        $queueData = $greenApi->showMessagesQueue();
        $queueCount = count($queueData['queue'] ?? []);

        // Opted-out count
        $optedOutCount = CommunicationOptOut::where('channel', 'whatsapp')->count();

        // Templates
        $templates = MessageTemplate::active()->forChannel('whatsapp')->latest()->get();

        // Active campaigns
        $campaigns = WhatsappCampaign::withCount(['recipients as total_r', 'recipients as sent_r' => fn($q) => $q->whereIn('status', ['sent']), 'recipients as failed_r' => fn($q) => $q->whereIn('status', ['failed', 'invalid']), 'recipients as pending_r' => fn($q) => $q->where('status', 'pending')])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        // Today's sent count
        $todaySent = CommunicationsLog::where('channel', 'whatsapp')->where('direction', 'outgoing')->whereDate('created_at', today())->count();

        return view('admin.crm.communications.whatsapp', compact(
            'contacts', 'companies', 'stats', 'waState',
            'warmupDays', 'isWarmingUp', 'responseRatio', 'outgoing', 'incoming',
            'queueCount', 'optedOutCount', 'templates', 'campaigns', 'todaySent'
        ));
    }

    /**
     * Create a WhatsApp campaign and dispatch the first job.
     */
    public function sendWhatsapp(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'recipients'           => 'nullable|array',
            'recipients.*'         => 'nullable|string',
            'bulk_numbers'         => 'nullable|string',
            'message'              => 'required_without:variant_a_message|string|max:4096|nullable',
            'campaign_name'        => 'required|string|max:255',
            'delay_min'            => 'required|integer|min:3|max:300',
            'delay_max'            => 'required|integer|min:3|max:600',
            'daily_limit'          => 'required|integer|min:50|max:2000',
            'scheduled_at'         => 'nullable|date|after:now',
            'is_ab_test'           => 'nullable|boolean',
            'variant_a_message'    => 'required_with:is_ab_test|string|max:4096|nullable',
            'variant_b_message'    => 'required_with:is_ab_test|string|max:4096|nullable',
            'split_percent'        => 'nullable|integer|min:10|max:90',
            'alert_threshold'      => 'nullable|integer|min:0|max:100',
            'auto_send_winner'     => 'nullable|boolean',
            'winner_decision_min_replies' => 'nullable|integer|min:5|max:500',
            'winner_decision_delay_hours' => 'nullable|integer|min:1|max:72',
        ]);

        // Collect all recipients
        $allPhones = [];

        if (!empty($validated['recipients'])) {
            foreach ($validated['recipients'] as $phone) {
                $allPhones[] = ['phone' => $this->normalizePhone($phone), 'name' => null];
            }
        }

        if (!empty($validated['bulk_numbers'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['bulk_numbers']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $name = null;
                $phone = $line;
                if (str_contains($line, ',')) {
                    $parts = array_map('trim', explode(',', $line, 2));
                    $name = $parts[0];
                    $phone = $parts[1] ?? $parts[0];
                } elseif (str_contains($line, '|')) {
                    $parts = array_map('trim', explode('|', $line, 2));
                    $name = $parts[0];
                    $phone = $parts[1] ?? $parts[0];
                }
                $phone = $this->normalizePhone($phone);
                if ($phone) $allPhones[] = ['phone' => $phone, 'name' => $name];
            }
        }

        $seen = [];
        $recipients = [];
        foreach ($allPhones as $r) {
            if (!in_array($r['phone'], $seen)) {
                $seen[] = $r['phone'];
                $recipients[] = $r;
            }
        }

        if (count($recipients) === 0) {
            return back()->with('error', 'No valid recipients found. Select contacts or paste numbers.');
        }

        $isAbTest = !empty($validated['is_ab_test']);
        $splitPercent = $validated['split_percent'] ?? 50;

        // Clear queue before campaign (best practice)
        $greenApi = app(GreenApiService::class);
        $greenApi->clearMessagesQueue();

        // Create campaign
        $campaignData = [
            'name'             => $validated['campaign_name'],
            'user_id'          => Auth::id(),
            'message'          => $isAbTest ? null : $validated['message'],
            'total_recipients' => count($recipients),
            'status'           => 'queued',
            'delay_min'        => $validated['delay_min'],
            'delay_max'        => $validated['delay_max'],
            'daily_limit'      => $validated['daily_limit'],
            'scheduled_at'     => $validated['scheduled_at'] ?? null,
            'is_ab_test'       => $isAbTest,
            'alert_threshold'  => $validated['alert_threshold'] ?? 0,
        ];

        if ($isAbTest) {
            $campaignData['variant_a_message'] = $validated['variant_a_message'];
            $campaignData['variant_b_message'] = $validated['variant_b_message'];
            $campaignData['split_percent'] = $splitPercent;
            $campaignData['auto_send_winner'] = !empty($validated['auto_send_winner']);
            $campaignData['winner_decision_min_replies'] = $validated['winner_decision_min_replies'] ?? 30;
            $campaignData['winner_decision_delay_hours'] = $validated['winner_decision_delay_hours'] ?? 24;
        }

        $campaign = WhatsappCampaign::create($campaignData);

        // Shuffle and split recipients for A/B test
        $variantA = floor(count($recipients) * ($splitPercent / 100));
        if ($isAbTest) {
            shuffle($recipients);
        }

        // Create recipients with A/B variant assignment
        foreach ($recipients as $idx => $r) {
            $variant = null;
            if ($isAbTest) {
                $variant = $idx < $variantA ? 'a' : 'b';
            }
            WhatsappCampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'phone'       => $r['phone'],
                'name'        => $r['name'],
                'variant'     => $variant,
                'status'      => 'pending',
            ]);
        }

        // Build success message
        $msg = "Campaign '{$campaign->name}' created with " . count($recipients) . " recipients";
        if ($isAbTest) {
            $msg .= " (A/B split: {$splitPercent}% A, " . (100 - $splitPercent) . "% B)";
        }
        if ($validated['scheduled_at'] ?? null) {
            $msg .= ". Scheduled for " . \Carbon\Carbon::parse($validated['scheduled_at'])->format('M d, Y H:i');
        } else {
            $msg .= ". Messages will send with {$validated['delay_min']}-{$validated['delay_max']}s delays.";
        }

        // Dispatch first job (if not scheduled, it will handle scheduling in the job)
        $firstRecipient = $campaign->recipients()->where('status', 'pending')->orderBy('id')->first();
        if ($firstRecipient) {
            SendWhatsappCampaignMessage::dispatch($campaign->id, $firstRecipient->id)
                ->delay(now()->addSeconds(3));
        }

        return redirect()->route('admin.crm.communications.whatsapp')
            ->with('success', $msg);
    }

    /**
     * Upload recipients via CSV.
     */
    public function uploadWhatsappRecipients(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');
        $phones = [];
        $row = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if ($row === 1) continue; // skip header if first row looks like header

            $name = trim($data[0] ?? '');
            $phone = trim($data[1] ?? ($data[0] ?? ''));

            // If only one column, treat as phone
            if (count($data) === 1) {
                $phone = trim($data[0]);
                $name = null;
            }

            $phone = $this->normalizePhone($phone);
            if ($phone && !in_array($phone, array_column($phones, 'phone'))) {
                $phones[] = ['phone' => $phone, 'name' => $name ?: null];
            }
        }
        fclose($handle);

        return response()->json([
            'count' => count($phones),
            'recipients' => $phones,
        ]);
    }

    /**
     * Pause a campaign.
     */
    public function pauseCampaign(WhatsappCampaign $campaign)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($campaign->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            return back()->with('error', 'Access denied.');
        }

        $campaign->update(['status' => 'paused']);
        return back()->with('success', "Campaign '{$campaign->name}' paused.");
    }

    /**
     * Resume a campaign.
     */
    public function resumeCampaign(WhatsappCampaign $campaign)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($campaign->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            return back()->with('error', 'Access denied.');
        }

        $campaign->update(['status' => 'queued']);

        // Re-dispatch first pending
        $first = $campaign->recipients()->where('status', 'pending')->orderBy('id')->first();
        if ($first) {
            SendWhatsappCampaignMessage::dispatch($campaign->id, $first->id)
                ->delay(now()->addSeconds(3));
        }

        return back()->with('success', "Campaign '{$campaign->name}' resumed.");
    }

    /**
     * Cancel a campaign.
     */
    public function cancelCampaign(WhatsappCampaign $campaign)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($campaign->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            return back()->with('error', 'Access denied.');
        }

        $campaign->update(['status' => 'cancelled']);
        return back()->with('success', "Campaign '{$campaign->name}' cancelled.");
    }

    /**
     * Campaign detail / progress page with A/B analytics.
     */
    public function showCampaign(WhatsappCampaign $campaign)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($campaign->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            return back()->with('error', 'Access denied.');
        }

        $campaign->loadCount([
            'recipients as total_recipients',
            'recipients as sent_count' => fn($q) => $q->where('status', 'sent'),
            'recipients as failed_count' => fn($q) => $q->whereIn('status', ['failed', 'invalid']),
            'recipients as pending_count' => fn($q) => $q->where('status', 'pending'),
            'recipients as opted_out_count' => fn($q) => $q->where('status', 'opted_out'),
        ]);

        // A/B analytics
        $abStats = null;
        if ($campaign->is_ab_test) {
            $variantASent = $campaign->recipients()->where('variant', 'a')->where('status', 'sent')->count();
            $variantBSent = $campaign->recipients()->where('variant', 'b')->where('status', 'sent')->count();
            $variantAReplies = $campaign->recipients()->where('variant', 'a')->whereNotNull('replied_at')->count();
            $variantBReplies = $campaign->recipients()->where('variant', 'b')->whereNotNull('replied_at')->count();

            $abStats = [
                'a_sent' => $variantASent,
                'b_sent' => $variantBSent,
                'a_replies' => $variantAReplies,
                'b_replies' => $variantBReplies,
                'a_rate' => $variantASent > 0 ? round(($variantAReplies / $variantASent) * 100, 1) : 0,
                'b_rate' => $variantBSent > 0 ? round(($variantBReplies / $variantBSent) * 100, 1) : 0,
                'winner' => null,
            ];

            if ($variantASent > 0 && $variantBSent > 0) {
                if ($abStats['a_rate'] > $abStats['b_rate']) {
                    $abStats['winner'] = 'A';
                } elseif ($abStats['b_rate'] > $abStats['a_rate']) {
                    $abStats['winner'] = 'B';
                }
            }
        }

        // Alerts
        $alerts = $campaign->alerts()->orderByDesc('created_at')->take(10)->get();

        return view('admin.crm.communications.whatsapp_campaign', compact('campaign', 'abStats', 'alerts'));
    }

    /**
     * Compare multiple campaigns side-by-side.
     */
    public function compareCampaigns(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $campaignIds = $request->input('campaigns', []);
        $campaigns = collect();

        if (!empty($campaignIds)) {
            $campaigns = WhatsappCampaign::whereIn('id', $campaignIds)
                ->where(function ($q) {
                    $q->where('user_id', Auth::id())
                        ->orWhereHas('user', fn($uq) => $uq->whereHas('roles', fn($rq) => $rq->where('name', 'super_admin')));
                })
                ->withCount([
                    'recipients as total_r',
                    'recipients as sent_r' => fn($q) => $q->where('status', 'sent'),
                    'recipients as failed_r' => fn($q) => $q->whereIn('status', ['failed', 'invalid']),
                    'recipients as reply_r' => fn($q) => $q->whereNotNull('replied_at'),
                ])
                ->get();
        }

        // All campaigns for selection dropdown
        $allCampaigns = WhatsappCampaign::where('user_id', Auth::id())
            ->orWhereHas('user', fn($uq) => $uq->whereHas('roles', fn($rq) => $rq->where('name', 'super_admin')))
            ->latest()
            ->take(50)
            ->get(['id', 'name', 'created_at']);

        return view('admin.crm.communications.whatsapp_compare', compact('campaigns', 'allCampaigns'));
    }

    /* ==================== WHATSAPP SETUP / STATUS ==================== */

    public function whatsappQr()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $greenApi = app(GreenApiService::class);
        $qrData = $greenApi->getQrCode();

        if (!cache('wa_first_auth_date')) {
            cache()->forever('wa_first_auth_date', now()->toDateTimeString());
        }

        return view('admin.crm.communications.whatsapp_qr', compact('qrData'));
    }

    public function whatsappStatus()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $greenApi = app(GreenApiService::class);
        return response()->json($greenApi->getState());
    }

    public function whatsappReceive()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $greenApi = app(GreenApiService::class);
        $notification = $greenApi->receiveNotification();

        if ($notification && isset($notification['receiptId'])) {
            $greenApi->deleteNotification($notification['receiptId']);

            $body = $notification['body'] ?? [];
            if (($body['typeWebhook'] ?? '') === 'incomingMessageReceived') {
                $messageData = $body['messageData'] ?? [];
                $text = $messageData['textMessageData']['textMessage'] ?? '';
                $sender = $body['senderData']['sender'] ?? '';
                $senderName = $body['senderData']['senderName'] ?? '';

                if ($text && $sender) {
                    CommunicationsLog::create([
                        'user_id'      => Auth::id(),
                        'channel'        => 'whatsapp',
                        'direction'      => 'incoming',
                        'sender_phone'   => $sender,
                        'message'        => $text,
                        'status'         => 'received',
                        'metadata'       => ['sender_name' => $senderName],
                    ]);

                    $lowerText = strtolower(trim($text));
                    if (in_array($lowerText, ['stop', 'unsubscribe', 'opt out', 'opt-out', 'cancel'])) {
                        CommunicationOptOut::firstOrCreate(
                            ['phone' => $sender, 'channel' => 'whatsapp'],
                            ['reason' => 'User sent STOP', 'opted_out_at' => now()]
                        );
                        $greenApi->sendMessage($sender, "You have been unsubscribed from Forus Freight WhatsApp messages. Reply START to resubscribe.");

                        return response()->json(['received' => true, 'action' => 'opted_out', 'notification' => $notification]);
                    }
                }
            }
        }

        return response()->json(['received' => !empty($notification), 'notification' => $notification]);
    }

    public function whatsappClearQueue()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $greenApi = app(GreenApiService::class);
        $ok = $greenApi->clearMessagesQueue();
        return back()->with($ok ? 'success' : 'error', $ok ? 'Message queue cleared.' : 'Failed to clear queue.');
    }

    /* ==================== TEMPLATES ==================== */

    public function storeTemplate(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'channel'  => 'required|string|in:sms,whatsapp,both',
            'category' => 'required|string|in:general,follow_up,promo,reminder,invoice,custom_clearance,onboarding,event',
            'body'     => 'required|string|max:4096',
        ]);

        MessageTemplate::create([
            'name'       => $validated['name'],
            'channel'    => $validated['channel'],
            'category'   => $validated['category'],
            'body'       => $validated['body'],
            'is_active'  => true,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Template saved successfully.');
    }

    public function updateTemplate(Request $request, MessageTemplate $template)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'channel'  => 'required|string|in:sms,whatsapp,both',
            'category' => 'required|string|in:general,follow_up,promo,reminder,invoice,custom_clearance,onboarding,event',
            'body'     => 'required|string|max:4096',
            'is_active'=> 'boolean',
        ]);

        $template->update($validated);
        return back()->with('success', 'Template updated.');
    }

    public function destroyTemplate(MessageTemplate $template)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $template->delete();
        return back()->with('success', 'Template deleted.');
    }

    /**
     * Download a sample CSV for bulk recipients.
     */
    public function downloadSampleCsv()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="forus_bulk_recipients_sample.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'phone']);
            fputcsv($handle, ['John Banda', '+260972345678']);
            fputcsv($handle, ['Mary Zulu', '+260973456789']);
            fputcsv($handle, ['David Mulenga', '+260964567890']);
            fputcsv($handle, ['Sarah Phiri', '+260975678901']);
            fputcsv($handle, ['James Tembo', '+260966789012']);
            fputcsv($handle, ['Grace Lungu', '+260977890123']);
            fputcsv($handle, ['Peter Chanda', '+260978901234']);
            fputcsv($handle, ['Linda Mwanza', '+260979012345']);
            fputcsv($handle, ['Robert Kabwe', '+260970123456']);
            fputcsv($handle, ['Esther Musonda', '+260971234567']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export actual contacts as CSV for editing and re-upload.
     */
    public function exportContactsCsv(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = User::where('is_admin', false)->whereNotNull('phone');
        if ($request->has('filter')) {
            match ($request->filter) {
                'leads'      => $query->where('crm_status', 'lead'),
                'active'     => $query->where('crm_status', 'active'),
                'high_value' => $query->where('crm_status', 'high_value'),
                'company'    => $query->whereNotNull('company_id'),
                default      => $query,
            };
        }

        $contacts = $query->orderBy('name')->get();
        $filterLabel = $request->filter ? ucfirst($request->filter) . '_' : '';
        $filename = 'forus_contacts_export_' . $filterLabel . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'phone', 'email', 'status', 'company', 'assigned_agent']);
            foreach ($contacts as $c) {
                fputcsv($handle, [
                    $c->name,
                    $c->phone,
                    $c->email ?? '',
                    $c->crm_status ?? '',
                    $c->company?->name ?? '',
                    $c->assignedAgent?->name ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ==================== HELPERS ==================== */

    private function contactStats(): array
    {
        $base = User::where('is_admin', false)->whereNotNull('phone');
        return [
            'total'      => (clone $base)->count(),
            'leads'      => (clone $base)->where('crm_status', 'lead')->count(),
            'active'     => (clone $base)->where('crm_status', 'active')->count(),
            'high_value' => (clone $base)->where('crm_status', 'high_value')->count(),
        ];
    }

    /**
     * Normalize phone number to international format.
     */
    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        if (empty($phone)) return null;

        // If starts with 0 and no +, assume Zambia (+260) or South Africa (+27) — default +260
        if (str_starts_with($phone, '0') && !str_starts_with($phone, '+')) {
            $phone = '+260' . ltrim($phone, '0');
        }

        // Ensure it starts with +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        // Basic length check (minimum 10 digits after +)
        if (strlen($phone) < 10) return null;

        return $phone;
    }
}
