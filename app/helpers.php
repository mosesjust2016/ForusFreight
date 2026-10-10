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

if (! function_exists('money')) {
    /**
     * Format an amount in the currency it was issued in.
     *
     * Invoice amounts carry their own currency (USD in practice), so they
     * must not go through the ZMW conversion in usd() — that turns a $85.50
     * invoice into $4.46. Figures with no currency (shipment costs, CRM
     * values) are ZMW and get the usual conversion.
     */
    function money(?float $amount, ?string $currency = null, int $decimals = 2): string
    {
        $amount ??= 0;
        $currency = $currency !== null ? strtoupper($currency) : null;

        if ($currency === 'USD') {
            return '$'.number_format($amount, $decimals);
        }

        if ($currency !== null && $currency !== 'ZMW') {
            return number_format($amount, $decimals).' '.$currency;
        }

        return usd($amount, $decimals);
    }
}
