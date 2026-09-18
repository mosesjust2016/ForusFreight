<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillShipmentStatuses extends Command
{
    protected $signature = 'shipments:backfill-statuses
                            {--dry-run : Report what would change without updating anything}';

    protected $description = 'Normalise legacy shipment/tracking-event statuses to canonical tracking_statuses codes';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $canonical = array_flip(Shipment::statusList());
        $legacyMap = config('forus.tracking_status_legacy_map', []);

        if (empty($legacyMap)) {
            $this->error('No legacy map configured in config/forus.php.');
            return self::FAILURE;
        }

        $this->line($dryRun
            ? 'DRY RUN — no records will be changed.'
            : 'Backfilling shipment statuses to canonical codes…');

        $shipmentUpdates = DB::transaction(function () use ($canonical, $legacyMap) {
            $updates = [];

            Shipment::query()
                ->whereNotNull('status')
                ->select(['id', 'status'])
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($canonical, $legacyMap, &$updates) {
                    foreach ($rows as $shipment) {
                        $status = (string) $shipment->status;

                        if (isset($canonical[$status])) {
                            continue;
                        }

                        if (isset($legacyMap[$status])) {
                            $updates[] = ['model' => 'shipment', 'id' => $shipment->id, 'from' => $status, 'to' => $legacyMap[$status]];
                        }
                    }
                });

            return $updates;
        });

        $eventUpdates = DB::transaction(function () use ($canonical, $legacyMap) {
            $updates = [];

            TrackingEvent::query()
                ->whereNotNull('status')
                ->select(['id', 'status'])
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($canonical, $legacyMap, &$updates) {
                    foreach ($rows as $event) {
                        $status = (string) $event->status;

                        if (isset($canonical[$status])) {
                            continue;
                        }

                        if (isset($legacyMap[$status])) {
                            $updates[] = ['model' => 'tracking_event', 'id' => $event->id, 'from' => $status, 'to' => $legacyMap[$status]];
                        }
                    }
                });

            return $updates;
        });

        $this->table(
            ['Model', 'ID', 'Legacy Status', 'Canonical Status'],
            collect(array_merge($shipmentUpdates, $eventUpdates))
                ->sortBy('id')
                ->map(fn ($u) => [$u['model'], $u['id'], $u['from'], $u['to']])
                ->values()
                ->all()
        );

        $this->info(sprintf(
            '%d shipment(s) and %d tracking event(s) %s.',
            count($shipmentUpdates),
            count($eventUpdates),
            $dryRun ? 'would be updated' : 'updated'
        ));

        if ($dryRun) {
            $this->info('Run without --dry-run to apply.');
            return self::SUCCESS;
        }

        if (empty($shipmentUpdates) && empty($eventUpdates)) {
            $this->info('Nothing to backfill — all statuses are already canonical.');
            return self::SUCCESS;
        }

        // Events are silenced so the ShipmentObserver does not re-send emails
        // or create duplicate TrackingEvents while we normalise historical rows.
        Shipment::withoutEvents(function () use ($shipmentUpdates, $eventUpdates) {
            foreach ($shipmentUpdates as $u) {
                Shipment::query()->whereKey($u['id'])->update(['status' => $u['to']]);
            }

            foreach ($eventUpdates as $u) {
                TrackingEvent::query()->whereKey($u['id'])->update(['status' => $u['to']]);
            }
        });

        return self::SUCCESS;
    }
}