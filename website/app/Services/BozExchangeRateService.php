<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BozExchangeRateService
{
    protected string $apiUrl = 'https://www.boz.zm/api/v1/views/boz_zmw_usd_daily_exchange_rates';

    /**
     * Fetch the latest exchange rates from BOZ.
     */
    public function fetchLatest(): ?array
    {
        try {
            $response = Http::timeout(15)->get($this->apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && count($data) > 0) {
                    $latest = $data[0];
                    return [
                        'buying' => (float) $latest['buying'],
                        'mid_rate' => (float) $latest['mid_rate'],
                        'selling' => (float) $latest['selling'],
                        'recorded_at' => $this->parseTime($latest['time']),
                    ];
                }
            }

            Log::warning('BOZ API returned unexpected response', ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('BOZ API fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch and store the latest rate in the database.
     */
    public function sync(): ?ExchangeRate
    {
        $latest = $this->fetchLatest();

        if (! $latest) {
            return null;
        }

        return ExchangeRate::create([
            'base_currency' => 'USD',
            'quote_currency' => 'ZMW',
            'buying_rate' => $latest['buying'],
            'mid_rate' => $latest['mid_rate'],
            'selling_rate' => $latest['selling'],
            'source' => 'boz',
            'recorded_at' => $latest['recorded_at'],
        ]);
    }

    /**
     * Get the most recent exchange rate from the database.
     */
    public function getCurrentRate(): ?ExchangeRate
    {
        return ExchangeRate::latest('recorded_at')->first();
    }

    /**
     * Convert USD to ZMW using the latest mid rate.
     */
    public function convertUsdToZmw(float $amount): ?float
    {
        $rate = $this->getCurrentRate();

        if (! $rate) {
            return null;
        }

        return round($amount * $rate->mid_rate, 2);
    }

    /**
     * Convert ZMW to USD using the latest mid rate.
     */
    public function convertZmwToUsd(float $amount): ?float
    {
        $rate = $this->getCurrentRate();

        if (! $rate) {
            return null;
        }

        return round($amount / $rate->mid_rate, 2);
    }

    /**
     * Parse the HTML datetime string from BOZ.
     */
    protected function parseTime(string $timeHtml): Carbon
    {
        // The time field contains HTML like:
        // <time datetime="2026-05-15T13:30:00Z" class="datetime">15 May 2026 - 15:30</time>
        if (preg_match('/datetime="([^"]+)"/', $timeHtml, $matches)) {
            return Carbon::parse($matches[1]);
        }

        return Carbon::now();
    }
}
