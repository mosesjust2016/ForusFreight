<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PacraCompanyService
{
    private static ?float $lastRequestAt = null;

    public function search(string $search): array
    {
        $minimumDelay = max(0, config('services.pacra.min_delay_ms', 1200)) / 1000;
        if (self::$lastRequestAt !== null) {
            $remaining = $minimumDelay - (microtime(true) - self::$lastRequestAt);
            if ($remaining > 0) usleep((int) ($remaining * 1_000_000));
        }

        self::$lastRequestAt = microtime(true);

        return Http::timeout(15)
            ->acceptJson()
            ->retry(
                config('services.pacra.retry_times', 3),
                function (int $attempt, \Throwable $exception) {
                    // Exponential backoff with a small random component keeps
                    // retries from arriving in a fixed burst.
                    return min(30000, (1000 * (2 ** ($attempt - 1))) + random_int(100, 700));
                },
                function (\Throwable $exception) {
                    return $exception instanceof \Illuminate\Http\Client\RequestException
                        && ($exception->response?->status() === 429 || $exception->response?->serverError());
                }
            )
            ->get(config('services.pacra.registry_search_url'), [
                'Searchtext' => $search,
                'SortyBy' => 'entity name',
                'Direction' => 'ASC',
            ])
            ->throw()
            ->json();
    }
}
