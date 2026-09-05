<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'quote_currency',
        'buying_rate',
        'mid_rate',
        'selling_rate',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'buying_rate' => 'decimal:4',
        'mid_rate' => 'decimal:4',
        'selling_rate' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    /**
     * The most recent USD/ZMW mid rate (ZMW per 1 USD), cached briefly so
     * every currency conversion on a page doesn't hit the database
     * separately. Null if no rate has ever been synced.
     */
    public static function latestMidRate(): ?float
    {
        return Cache::remember('latest_exchange_rate_mid', now()->addMinutes(10), function () {
            $rate = static::latest('recorded_at')->first();

            return $rate ? (float) $rate->mid_rate : null;
        });
    }
}
