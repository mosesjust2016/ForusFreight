<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use Illuminate\Console\Command;

class RepairCorruptedShipmentSerials extends Command
{
    /**
     * A bulk import whose source file was missing the "Serial Number"
     * column shifted every field one column left, so serial_no ended up
     * holding the origin value instead of a real code. This is the exact
     * signature that corruption left behind.
     */
    private const CORRUPTION_SIGNATURE = 'China (Guangzhou Port)%';

    protected $signature = 'shipments:repair-serials {--dry-run : Report what would change without saving anything}';

    protected $description = 'Replace corrupted serial numbers (origin text saved as serial_no by a past bulk-import bug) with freshly generated ones';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $shipments = Shipment::where('serial_no', 'like', self::CORRUPTION_SIGNATURE)->get();

        if ($shipments->isEmpty()) {
            $this->info('No corrupted serial numbers found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Found {$shipments->count()} shipment(s) with a corrupted serial number.");
        $this->newLine();

        $fixedWithTracking = 0;
        $fixedWithoutTracking = [];

        foreach ($shipments as $shipment) {
            $newSerial = $this->generateSerialNo();
            $hasTracking = !empty($shipment->tracking_number);

            $this->line(sprintf(
                '  #%d  %-30s  %s  ->  %s%s',
                $shipment->id,
                $shipment->client_name ?: '(no client name)',
                $shipment->serial_no,
                $newSerial,
                $hasTracking ? '' : '   [NEEDS REAL TRACKING NUMBER — none on record]'
            ));

            if (!$dryRun) {
                $shipment->update(['serial_no' => $newSerial]);
            }

            if ($hasTracking) {
                $fixedWithTracking++;
            } else {
                $fixedWithoutTracking[] = $shipment;
            }
        }

        $this->newLine();
        $this->info("Fully fixed (had a real tracking number on record): {$fixedWithTracking}");

        if (!empty($fixedWithoutTracking)) {
            $this->warn(count($fixedWithoutTracking) . ' shipment(s) got a clean internal serial number, but have no real tracking number anywhere in the database. These customers\' actual tracking codes were never captured and can only be restored from the original source file:');
            foreach ($fixedWithoutTracking as $shipment) {
                $this->line("  #{$shipment->id}  {$shipment->client_name}  (created {$shipment->created_at->format('Y-m-d')})");
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Dry run only — nothing was saved. Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    private function generateSerialNo(): string
    {
        do {
            $candidate = 'ZML-' . strtoupper(substr(uniqid(), -8));
        } while (Shipment::where('serial_no', $candidate)->exists());

        return $candidate;
    }
}
