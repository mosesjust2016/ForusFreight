<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappCampaign;
use App\Models\CampaignAlert;

class CheckCampaignAlerts extends Command
{
    protected $signature = 'whatsapp:check-campaign-alerts';
    protected $description = 'Check campaign response rates against thresholds and create alerts';

    public function handle(): int
    {
        $campaigns = WhatsappCampaign::where('alert_threshold', '>', 0)
            ->whereIn('status', ['sending', 'paused', 'completed'])
            ->get();

        $alertsCreated = 0;

        foreach ($campaigns as $campaign) {
            $sent = $campaign->sent_count;
            if ($sent === 0) continue;

            // Only count replies from sent recipients
            $replies = $campaign->recipients()->whereNotNull('replied_at')->count();
            $rate = ($replies / $sent) * 100;
            $threshold = $campaign->alert_threshold;

            // Check if already alerted for this campaign+threshold
            $alreadyAlerted = CampaignAlert::where('campaign_id', $campaign->id)
                ->where('type', 'threshold_met')
                ->exists();

            if ($rate >= $threshold && !$alreadyAlerted) {
                CampaignAlert::create([
                    'campaign_id' => $campaign->id,
                    'type' => 'threshold_met',
                    'message' => "Campaign '{$campaign->name}' reached a {$rate}% response rate (threshold: {$threshold}%).",
                    'data' => [
                        'response_rate' => round($rate, 1),
                        'threshold' => $threshold,
                        'replies' => $replies,
                        'sent' => $sent,
                    ],
                ]);

                $this->info("Alert: Campaign #{$campaign->id} hit {$rate}% response rate (threshold: {$threshold}%).");
                $alertsCreated++;
            }

            // Check if exceeded 2x threshold (celebration alert)
            $alreadyExceeded = CampaignAlert::where('campaign_id', $campaign->id)
                ->where('type', 'threshold_exceeded')
                ->exists();

            if ($rate >= ($threshold * 2) && !$alreadyExceeded && $threshold > 0) {
                CampaignAlert::create([
                    'campaign_id' => $campaign->id,
                    'type' => 'threshold_exceeded',
                    'message' => "Amazing! Campaign '{$campaign->name}' doubled its target with a {$rate}% response rate!",
                    'data' => [
                        'response_rate' => round($rate, 1),
                        'threshold' => $threshold,
                        'replies' => $replies,
                        'sent' => $sent,
                    ],
                ]);
                $this->info("Alert: Campaign #{$campaign->id} exceeded 2x threshold!");
                $alertsCreated++;
            }
        }

        $this->info("Created {$alertsCreated} alert(s).");
        return self::SUCCESS;
    }
}
