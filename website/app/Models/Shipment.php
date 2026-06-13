<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tracking_number', 'status', 'from', 'to', 'service', 'history',
        'origin', 'destination', 'weight', 'dimensions', 'description', 'service_type',
        'estimated_delivery', 'cost', 'border_status', 'images',
    ];

    protected $casts = [
        'history' => 'array',
        'images' => 'array',
        'shipment_date' => 'datetime',
        'estimated_delivery' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function trackingEvents() {
        return $this->hasMany(TrackingEvent::class);
    }

    public function hedge() {
        return $this->hasOne(CurrencyHedge::class);
    }
}
