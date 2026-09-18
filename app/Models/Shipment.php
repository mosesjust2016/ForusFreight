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
        'reference', 'phone_number',
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
     * Next auto-generated serial number in the form "ZMFFL-000001".
     * Increments from the highest ZMFFL serial currently on record, so
     * every generated serial is guaranteed unique. 6 digits means:
     * 000001 → 999999 (999,999 shipments) before the format ever needs
     * to change.
     */
    public static function nextSerialNumber(): string
    {
        $needle = config('forus.parcel_code_prefix', 'ZMFFL') . '-';

        $max = (int) static::query()
            ->where('serial_no', 'like', $needle . '%')
            ->get('serial_no')
            ->map(fn ($s) => (int) substr((string) $s->serial_no, strlen($needle)))
            ->max();

        return $needle . str_pad((string) ($max + 1), 6, '0', STR_PAD_LEFT);
    }

    /**
     * List of canonical status codes (stored values).
     */
    public static function statusList(): array
    {
        return array_keys(config('forus.tracking_statuses', []));
    }

    /**
     * code => customer-facing label, in timeline order.
     */
    public static function statusLabels(): array
    {
        return config('forus.tracking_statuses', []);
    }

    /**
     * Resolve a stored (possibly legacy) status to its canonical code.
     */
    public static function canonicalStatus(?string $status): ?string
    {
        if (!$status) {
            return null;
        }

        $statuses = config('forus.tracking_statuses', []);
        if (isset($statuses[$status])) {
            return $status;
        }

        $legacy = config('forus.tracking_status_legacy_map', []);

        return $legacy[$status] ?? null;
    }

    /**
     * All stored status values (canonical + legacy spellings) that map onto
     * the given canonical codes — useful for whereIn() filters so queries
     * match both new rows and existing legacy rows.
     */
    public static function statusesForCanonical(array $codes): array
    {
        $codes = array_flip($codes);

        $values = [];
        foreach (config('forus.tracking_statuses', []) as $code => $label) {
            if (isset($codes[$code])) {
                $values[] = $code;
            }
        }
        foreach (config('forus.tracking_status_legacy_map', []) as $legacy => $code) {
            if (isset($codes[$code])) {
                $values[] = $legacy;
            }
        }

        return array_values(array_unique($values));
    }

    /**
     * Customer-facing label for any stored status string (legacy-aware).
     * Useful for tracking events and arbitrary status values.
     */
    public static function statusLabelFor(?string $status): ?string
    {
        if (!$status) {
            return null;
        }

        $statuses = config('forus.tracking_statuses', []);
        if (isset($statuses[$status])) {
            return $statuses[$status];
        }

        $code = self::canonicalStatus($status);

        return $code && isset($statuses[$code]) ? $statuses[$code] : $status;
    }

    /**
     * Customer-facing label for the stored status. Handles legacy values
     * (e.g. "Order Placed", "IN TRANSIT") so older rows keep displaying
     * recognisable labels until they are updated to a canonical code.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(get: fn () => self::statusLabelFor($this->status));
    }

    /**
     * True when the shipment is terminal (delivered, cancelled/exception).
     */
    public function isTerminalStatus(): bool
    {
        $code = self::canonicalStatus($this->status);

        return in_array($code, ['DELIVERED', 'EXCEPTION'], true);
    }

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
            if ($this->status === 'Delivered' || self::canonicalStatus($this->status) === 'DELIVERED') {
                return 100;
            }

            $dateProgress = $this->dateOfLoadBasedProgress();

            if ($dateProgress !== null) {
                return $dateProgress;
            }

            $progress = config('forus.tracking_status_progress', []);
            $code = self::canonicalStatus($this->status);

            return $code && isset($progress[$code]) ? $progress[$code] : 0;
        });
    }

    /**
     * Date-based progress (elapsed between Date Loaded and ETA), or null
     * when there isn't enough date data to calculate it.
     */
    private function dateOfLoadBasedProgress(): ?int
    {
        if (!$this->date_of_load || !$this->estimated_delivery) {
            return null;
        }

        $total = $this->date_of_load->diffInMinutes($this->estimated_delivery);
        $elapsed = $this->date_of_load->diffInMinutes(Carbon::now());

        if ($total <= 0) {
            return null;
        }

        return (int) max(0, min(100, round(($elapsed / $total) * 100)));
    }

    /**
     * "Current location" shown on the tracking page. Falls back to the
     * latest tracking event's location when current_border was never
     * manually set — admins add tracking events with a location on every
     * real update but rarely also duplicate it into current_border.
     */
    protected function currentLocationDisplay(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->current_border) {
                return $this->current_border;
            }

            $latest = $this->relationLoaded('trackingEvents')
                ? $this->trackingEvents->sortByDesc(fn ($e) => $e->sequence ?? $e->event_time)->first()
                : $this->trackingEvents()->orderByDesc('sequence')->orderByDesc('event_time')->first();

            return $latest?->location;
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
