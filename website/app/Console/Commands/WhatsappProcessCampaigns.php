<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWhatsappCampaignMessage;
use App\Models\WhatsappCampaign;

class WhatsappProcessCampaigns extends Command
{
    protected $signature = 'whatsapp:process-campaigns {--campaign= : Process a specific campaign ID} {--limit=50 : Max messages to send in this run}';
    protected $description = 'Manually process pending WhatsApp campaigns (for users not running queue workers)';

    public function handle(): int
    {
        $query = WhatsappCampaign::whereIn('status', ['queued', 'sending']);

        if ($this->option('campaign')) {
            $query->where('id', $this->option('campaign'));
        }

        $campaigns = $query->get();

        if ($campaigns->isEmpty()) {
            $this->info('No active WhatsApp campaigns to process.');
            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $processed = 0;

        foreach ($campaigns as $campaign) {
            $this->info("Processing campaign: {$campaign->name} (ID: {$campaign->id})");

            $pending = $campaign->recipients()->where('status', 'pending')->orderBy('id')->limit($limit)->get();

            foreach ($pending as $recipient) {
                if ($processed >= $limit) {
                    $this->info("Limit of {$limit} messages reached. Stopping.");
                    return self::SUCCESS;
                }

                // Dispatch immediately (no delay in manual mode)
                SendWhatsappCampaignMessage::dispatch($campaign->id, $recipient->id);
                $processed++;
                $this->info("  Dispatched to {$recipient->phone}");

                // Sleep for random delay to respect rate limits
                $sleepSeconds = rand($campaign->delay_min, $campaign->delay_max);
                $this->info("  Sleeping {$sleepSeconds}s...");
                sleep($sleepSeconds);
            }
        }

        $this->info("Done. Dispatched {$processed} message(s).");
        return self::SUCCESS;
    }
}
