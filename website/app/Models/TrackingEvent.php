<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id', 'location', 'description', 'status', 'latitude', 'longitude', 'event_time', 'sequence'
    ];

    protected $casts = [
        'event_time' => 'datetime',
    ];

    public function shipment() {
        return $this->belongsTo(Shipment::class);
    }
}
