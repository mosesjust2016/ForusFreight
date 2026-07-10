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
        'estimated_delivery', 'cost', 'border_status', 'images', 'quantity', 'driver',
        'vehicle_registration', 'shipment_date', 'current_border',
        'client_name', 'serial_no', 'code', 'client_phone', 'date_of_load',
        'no_of_parcels', 'cbm_volume', 'gross_weight', 'shipping_method',
        'port_of_origin', 'port_destination',
    ];

    protected $casts = [
        'history' => 'array',
        'images' => 'array',
        'shipment_date' => 'datetime',
        'estimated_delivery' => 'datetime',
        'date_of_load' => 'date',
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
