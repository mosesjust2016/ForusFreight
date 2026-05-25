<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\GreenApiService;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignRecipient;
use App\Models\CommunicationsLog;
use App\Models\CommunicationOptOut;

class SendWhatsappCampaignMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    public function __construct(
        public int $campaignId,
        public int $recipientId
    ) {}

    public function handle(): void
    {
        $campaign = WhatsappCampaign::find($this->campaignId);
        $recipient = WhatsappCampaignRecipient::find($this->recipientId);

        if (!$campaign || !$recipient || $recipient->status !== 'pending') {
            return;
        }

        // Check if campaign is paused or cancelled
        if (in_array($campaign->status, ['paused', 'cancelled'])) {
            return;
        }

        // Handle scheduled campaigns — if not yet time, re-dispatch later
        if ($campaign->scheduled_at && $campaign->scheduled_at->isFuture()) {
            $secondsUntil = now()->diffInSeconds($campaign->scheduled_at);
            Log::info("Campaign {$campaign->id} scheduled for later. Re-dispatching in {$secondsUntil}s.");
            self::dispatch($campaign->id, $recipient->id)
                ->delay(now()->addSeconds(min($secondsUntil, 300))); // check again in 5 min max
            return;
        }

        // Daily limit check
        $todaySent = CommunicationsLog::where('channel', 'whatsapp')
            ->where('direction', 'outgoing')
            ->whereDate('created_at', today())
            ->count();

        if ($todaySent >= $campaign->daily_limit) {
            Log::info("WhatsApp daily limit reached for campaign {$campaign->id}. Pausing.");
            $campaign->update(['status' => 'paused']);
            return;
        }

        $greenApi = app(GreenApiService::class);

        // Check opt-out
        $optedOut = CommunicationOptOut::where('phone', $recipient->phone)
            ->where('channel', 'whatsapp')
            ->exists();

        if ($optedOut) {
            $recipient->update(['status' => 'opted_out']);
            $campaign->increment('opted_out_count');
            $this->dispatchNext($campaign);
            return;
        }

        // Check WhatsApp exists
        $check = $greenApi->checkWhatsApp($recipient->phone);
        if (!($check['exists'] ?? false)) {
            $recipient->update(['status' => 'invalid', 'error_message' => 'No WhatsApp account']);
            $campaign->increment('failed_count');
            $this->dispatchNext($campaign);
            return;
        }

        // Determine message: A/B test or standard
        if ($campaign->is_ab_test && $recipient->variant) {
            $message = $recipient->variant === 'a' ? $campaign->variant_a_message : $campaign->variant_b_message;
        } else {
            $message = $campaign->message;
        }

        // Personalize
        $message = str_replace('{name}', $recipient->name ?? 'there', $message);
        $message = str_replace('{phone}', $recipient->phone, $message);

        // Add slight variation to avoid identical bulk detection
        $message = $this->addVariation($message, $recipient->id);

        // Send
        $res = $greenApi->sendMessage($recipient->phone, $message);

        CommunicationsLog::create([
            'user_id'         => $campaign->user_id,
            'channel'           => 'whatsapp',
            'direction'         => 'outgoing',
            'recipient_phone'   => $recipient->phone,
            'message'           => $message,
            'status'            => $res['success'] ? 'sent' : 'failed',
            'external_id'       => $res['data']['idMessage'] ?? null,
            'metadata'          => $res['success'] ? null : ['error' => $res['message'] ?? 'Unknown error'],
        ]);

        if ($res['success']) {
            $recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
                'external_id' => $res['data']['idMessage'] ?? null,
            ]);
            $campaign->increment('sent_count');

            // Track variant sent counts for A/B
            if ($campaign->is_ab_test && $recipient->variant) {
                $column = $recipient->variant === 'a' ? 'variant_a_sent' : 'variant_b_sent';
                $campaign->increment($column);
            }
        } else {
            $recipient->update(['status' => 'failed', 'error_message' => $res['message'] ?? 'Send failed']);
            $campaign->increment('failed_count');
        }

        $this->dispatchNext($campaign);
    }

    private function dispatchNext(WhatsappCampaign $campaign): void
    {
        $pendingCount = $campaign->recipients()->where('status', 'pending')->count();

        if ($pendingCount === 0) {
            $campaign->update(['status' => 'completed', 'completed_at' => now()]);
            Log::info("WhatsApp campaign {$campaign->id} completed.");
            return;
        }

        if ($campaign->status === 'queued') {
            $campaign->update(['status' => 'sending', 'started_at' => now()]);
        }

        $nextRecipient = $campaign->recipients()->where('status', 'pending')->orderBy('id')->first();
        if (!$nextRecipient) return;

        // If campaign is scheduled and not yet time, hold until scheduled time
        if ($campaign->scheduled_at && $campaign->scheduled_at->isFuture()) {
            $delaySeconds = now()->diffInSeconds($campaign->scheduled_at);
            self::dispatch($campaign->id, $nextRecipient->id)
                ->delay(now()->addSeconds($delaySeconds));
            return;
        }

        // Random delay between min and max seconds
        $delaySeconds = rand($campaign->delay_min, $campaign->delay_max);

        self::dispatch($campaign->id, $nextRecipient->id)
            ->delay(now()->addSeconds($delaySeconds));
    }

    private function addVariation(string $message, int $seed): string
    {
        $greetings = ['Hi', 'Hello', 'Hey', 'Good day'];
        $closings = ['Best regards', 'Regards', 'Thanks', 'Cheers'];
        $emojis = ['', ' 👋', ' ✨', ' 🚛'];

        srand($seed);
        $greeting = $greetings[array_rand($greetings)];
        $closing = $closings[array_rand($closings)];
        $emoji = $emojis[array_rand($emojis)];
        srand();

        if (str_contains($message, '{name}')) {
            return $message;
        }

        $lines = explode("\n", $message);
        if (count($lines) > 0 && str_starts_with(strtolower(trim($lines[0])), 'hi')) {
            $lines[0] = $greeting . substr($lines[0], 2) . $emoji;
        }

        return implode("\n", $lines);
    }
}
