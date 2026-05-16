<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
