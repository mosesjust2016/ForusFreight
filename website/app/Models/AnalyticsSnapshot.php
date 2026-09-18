<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date', 'total_contacts', 'total_deals', 'total_tasks',
        'open_tickets', 'pipeline_value', 'revenue_forecast', 'won_revenue',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'pipeline_value' => 'decimal:2',
        'revenue_forecast' => 'decimal:2',
        'won_revenue' => 'decimal:2',
    ];
}
