<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GreenApiService;
use App\Models\CommunicationsLog;
use App\Models\CommunicationOptOut;
use App\Models\WhatsappCampaignRecipient;

class WhatsappPollIncoming extends Command
{
    protected $signature = 'whatsapp:poll-incoming';
    protected $description = 'Poll Green API for incoming WhatsApp messages and process opt-outs';

    public function handle(): int
    {
        $greenApi = app(GreenApiService::class);
        $processed = 0;
        $optOuts = 0;
        $replies = 0;
        $maxIterations = 50;

        for ($i = 0; $i < $maxIterations; $i++) {
            $notification = $greenApi->receiveNotification();

            if (empty($notification) || !isset($notification['receiptId'])) {
                break;
            }

            $receiptId = $notification['receiptId'];
            $body = $notification['body'] ?? [];

            if (($body['typeWebhook'] ?? '') === 'incomingMessageReceived') {
                $messageData = $body['messageData'] ?? [];
                $text = $messageData['textMessageData']['textMessage'] ?? '';
                $sender = $body['senderData']['sender'] ?? '';
                $senderName = $body['senderData']['senderName'] ?? '';

                if ($text && $sender) {
                    CommunicationsLog::create([
                        'user_id'       => null,
                        'channel'       => 'whatsapp',
                        'direction'     => 'incoming',
                        'sender_phone'  => $sender,
                        'message'       => $text,
                        'status'        => 'received',
                        'metadata'      => ['sender_name' => $senderName],
                    ]);

                    $this->info("Received from {$sender}: {$text}");

                    // Track reply for A/B campaign analytics
                    $senderNormalized = preg_replace('/[^\d+]/', '', $sender);
                    $campaignRecipient = WhatsappCampaignRecipient::where('phone', $senderNormalized)
                        ->where('status', 'sent')
                        ->whereNull('replied_at')
                        ->latest('sent_at')
                        ->first();

                    if ($campaignRecipient) {
                        $campaignRecipient->update(['replied_at' => now()]);
                        $this->info("  Tracked reply for campaign #{$campaignRecipient->campaign_id} variant {$campaignRecipient->variant}");
                        $replies++;
                    }

                    // Handle STOP / unsubscribe
                    $lowerText = strtolower(trim($text));
                    if (in_array($lowerText, ['stop', 'unsubscribe', 'opt out', 'opt-out', 'cancel'])) {
                        CommunicationOptOut::firstOrCreate(
                            ['phone' => $sender, 'channel' => 'whatsapp'],
                            ['reason' => 'User sent STOP', 'opted_out_at' => now()]
                        );

                        $greenApi->sendMessage($sender, "You have been unsubscribed from Forus Freight WhatsApp messages. Reply START to resubscribe.");
                        $this->warn("Opted out: {$sender}");
                        $optOuts++;
                    }
                }
            }

            $greenApi->deleteNotification($receiptId);
            $processed++;
        }

        $this->info("Processed {$processed} notification(s), {$replies} reply/replies, {$optOuts} opt-out(s).");
        return self::SUCCESS;
    }
}
