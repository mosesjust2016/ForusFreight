<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignRecipient;
use App\Models\CampaignAlert;
use App\Jobs\SendWhatsappCampaignMessage;

class ResolveAbWinners extends Command
{
    protected $signature = 'whatsapp:resolve-ab-winners';
    protected $description = 'Check A/B test campaigns, declare winners, and auto-send winning variant to remaining recipients';

    public function handle(): int
    {
        $campaigns = WhatsappCampaign::where('is_ab_test', true)
            ->whereNull('winner_variant')
            ->whereIn('status', ['queued', 'sending', 'paused'])
            ->whereNotNull('started_at')
            ->get();

        $resolved = 0;

        foreach ($campaigns as $campaign) {
            $hoursElapsed = $campaign->started_at->diffInHours(now());
            $minReplies = $campaign->winner_decision_min_replies;
            $delayHours = $campaign->winner_decision_delay_hours;

            // Need minimum time AND minimum replies
            if ($hoursElapsed < $delayHours) {
                $this->info("Campaign #{$campaign->id}: too early ({$hoursElapsed}h / {$delayHours}h required).");
                continue;
            }

            $aSent = $campaign->recipients()->where('variant', 'a')->where('status', 'sent')->count();
            $bSent = $campaign->recipients()->where('variant', 'b')->where('status', 'sent')->count();
            $aReplies = $campaign->recipients()->where('variant', 'a')->whereNotNull('replied_at')->count();
            $bReplies = $campaign->recipients()->where('variant', 'b')->whereNotNull('replied_at')->count();

            $totalReplies = $aReplies + $bReplies;
            if ($totalReplies < $minReplies) {
                $this->info("Campaign #{$campaign->id}: not enough replies ({$totalReplies} / {$minReplies}).");
                continue;
            }

            // Determine winner by response rate
            $aRate = $aSent > 0 ? ($aReplies / $aSent) : 0;
            $bRate = $bSent > 0 ? ($bReplies / $bSent) : 0;
            $winner = $aRate >= $bRate ? 'a' : 'b';
            $loser = $winner === 'a' ? 'b' : 'a';

            $this->info("Campaign #{$campaign->id}: Winner is Variant {$winner} (A: {$aRate}%, B: {$bRate}%).");

            // Update campaign
            $campaign->update([
                'winner_variant' => $winner,
                'winner_declared_at' => now(),
            ]);

            // Create alert
            CampaignAlert::create([
                'campaign_id' => $campaign->id,
                'type' => 'winner_declared',
                'message' => "A/B winner declared: Variant {$winner} with " . ($winner === 'a' ? round($aRate * 100, 1) : round($bRate * 100, 1)) . "% response rate.",
                'data' => [
                    'variant_a_rate' => round($aRate * 100, 1),
                    'variant_b_rate' => round($bRate * 100, 1),
                    'winner' => $winner,
                    'total_replies' => $totalReplies,
                ],
            ]);

            // Auto-send winner to remaining pending recipients
            if ($campaign->auto_send_winner) {
                $pending = $campaign->recipients()->where('status', 'pending')->get();
                foreach ($pending as $recipient) {
                    $recipient->update(['variant' => $winner]);
                }
                $this->info("  Auto-updated {$pending->count()} pending recipients to Variant {$winner}.");

                // Kick off sending again if paused
                if ($campaign->status === 'paused') {
                    $campaign->update(['status' => 'queued']);
                    $first = $campaign->recipients()->where('status', 'pending')->orderBy('id')->first();
                    if ($first) {
                        SendWhatsappCampaignMessage::dispatch($campaign->id, $first->id)
                            ->delay(now()->addSeconds(3));
                    }
                }
            }

            $resolved++;
        }

        $this->info("Resolved {$resolved} A/B campaign(s).");
        return self::SUCCESS;
    }
}
