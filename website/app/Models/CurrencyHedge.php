<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyHedge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipment_id',
        'amount_usd',
        'hedged_rate',
        'amount_zmw',
        'hedge_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'hedged_rate' => 'decimal:4',
        'amount_zmw' => 'decimal:2',
        'hedge_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function markExpired(): void
    {
        if ($this->status === 'active' && $this->isExpired()) {
            $this->update(['status' => 'expired']);
        }
    }
}
