<?php

use App\Models\ExchangeRate;

if (! function_exists('usd')) {
    /**
     * Format a ZMW amount as USD using the latest synced BOZ mid rate.
     * Falls back to formatting the raw ZMW figure (never a bogus $0.00)
     * if no rate has been synced yet.
     */
    function usd(?float $zmwAmount, int $decimals = 2): string
    {
        $zmwAmount ??= 0;
        $rate = ExchangeRate::latestMidRate();

        if (! $rate) {
            return 'ZMW '.number_format($zmwAmount, $decimals);
        }

        return '$'.number_format($zmwAmount / $rate, $decimals);
    }
}
