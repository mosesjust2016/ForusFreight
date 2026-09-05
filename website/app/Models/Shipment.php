<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'serial_no', 'tracking_number', 'status', 'from', 'to', 'service', 'history',
        'origin', 'destination', 'weight', 'dimensions', 'description', 'service_type',
        'estimated_delivery', 'cost', 'border_status', 'images', 'quantity', 'driver',
        'vehicle_registration', 'shipment_date', 'current_border', 'next_action',
        'client_name', 'code', 'client_phone', 'date_of_load',
        'no_of_parcels', 'cbm_volume', 'gross_weight', 'shipping_method',
        'port_of_origin', 'port_destination', 'delivery_date', 'proof_of_delivery',
    ];

    protected $casts = [
        'history' => 'array',
        'images' => 'array',
        'shipment_date' => 'datetime',
        'estimated_delivery' => 'datetime',
        'date_of_load' => 'date',
        'delivery_date' => 'datetime',
    ];

    /**
     * Approximate progress-through-transit for each stage, used as a
     * fallback when there isn't enough date data to calculate a real
     * percentage. Keep in sync with the status list in
     * AdminController::createShipment().
     */
    private const STATUS_PROGRESS = [
        'Pending' => 5,
        'Order Placed' => 10,
        'In Transit' => 50,
        'At Border' => 65,
        'Cleared' => 75,
        'Out for Delivery' => 90,
        'Delivered' => 100,
        'Cancelled' => 0,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function trackingEvents() {
        // Ordered by event_time alone (not `sequence`): events created via
        // ShipmentObserver never set `sequence`, so sorting by it first would
        // push those NULLs ahead of real timestamps if a shipment ever has a
        // mix of both sources. The importer already keeps CSV rows with no
        // explicit date in order by synthesizing a monotonically increasing
        // event_time, so event_time alone is sufficient and safe for both.
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time');
    }

    public function hedge() {
        return $this->hasOne(CurrencyHedge::class);
    }

    /**
     * Days elapsed since loading — to the actual delivery date if
     * delivered, otherwise to now.
     */
    protected function daysInTransit(): Attribute
    {
        return Attribute::make(get: function () {
            if (!$this->date_of_load) {
                return null;
            }

            $end = $this->delivery_date ?? Carbon::now();

            return (int) $this->date_of_load->diffInDays($end);
        });
    }

    /**
     * Delivery progress percentage. Prefers a date-based calculation
     * (elapsed time between Date Loaded and ETA); falls back to a
     * status-stage estimate when either date is missing.
     */
    protected function deliveryProgressPercent(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->status === 'Delivered') {
                return 100;
            }

            if ($this->date_of_load && $this->estimated_delivery) {
                $total = $this->date_of_load->diffInMinutes($this->estimated_delivery);
                $elapsed = $this->date_of_load->diffInMinutes(Carbon::now());

                if ($total > 0) {
                    return (int) max(0, min(100, round(($elapsed / $total) * 100)));
                }
            }

            return self::STATUS_PROGRESS[$this->status] ?? 0;
        });
    }

    /**
     * Timestamp of the most recent tracking event, falling back to the
     * shipment's own last-updated time when it has no events.
     */
    protected function lastUpdateAt(): Attribute
    {
        return Attribute::make(get: function () {
            $latest = $this->relationLoaded('trackingEvents')
                ? $this->trackingEvents->sortByDesc(fn ($e) => $e->sequence ?? $e->event_time)->first()
                : $this->trackingEvents()->orderByDesc('sequence')->orderByDesc('event_time')->first();

            return $latest?->event_time ?? $this->updated_at;
        });
    }
}
