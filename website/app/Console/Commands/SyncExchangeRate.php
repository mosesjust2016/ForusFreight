<?php

namespace App\Console\Commands;

use App\Services\BozExchangeRateService;
use Illuminate\Console\Command;

class SyncExchangeRate extends Command
{
    protected $signature = 'exchange-rate:sync';

    protected $description = 'Fetch and store the latest USD/ZMW exchange rate from the Bank of Zambia';

    public function handle(BozExchangeRateService $service): int
    {
        $rate = $service->sync();

        if (! $rate) {
            $this->error('Failed to fetch exchange rate from BOZ.');

            return self::FAILURE;
        }

        $this->info("Synced: 1 USD = {$rate->mid_rate} ZMW (BOZ, recorded {$rate->recorded_at}).");

        return self::SUCCESS;
    }
}
