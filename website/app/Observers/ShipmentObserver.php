<?php

namespace App\Observers;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Services\BrevoMailService;
use App\Services\SmsService;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        try {
            app(BrevoMailService::class)->sendShipmentCreated($shipment);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send shipment created email', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);
        }

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $shipment->status,
            'description' => 'Shipment created and confirmed',
            'location' => $shipment->origin ?? 'Origin',
            'event_time' => now(),
        ]);
    }

    public function updated(Shipment $shipment): void
    {
        if ($shipment->wasChanged('status')) {
            $oldStatus = $shipment->getOriginal('status');
            $newStatus = $shipment->status;

            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $newStatus,
                'description' => "Status changed from '{$oldStatus}' to '{$newStatus}'",
                'location' => $shipment->origin ?? 'In Transit',
                'event_time' => now(),
            ]);

            try {
                app(BrevoMailService::class)->sendShipmentUpdated($shipment, $oldStatus, $newStatus);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send shipment status updated email', [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($shipment->user && $shipment->user->phone) {
                try {
                    $message = "Your Forus Freight shipment {$shipment->serial_no} has been {$newStatus}. Track: https://forusfreight.com/tracking";
                    app(SmsService::class)->sendShipmentUpdate($shipment->user->phone, $message);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send shipment status SMS', [
                        'shipment_id' => $shipment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function deleted(Shipment $shipment): void
    {
        \Illuminate\Support\Facades\Log::info('Shipment deleted', [
            'shipment_id' => $shipment->id,
            'serial_no' => $shipment->serial_no,
        ]);
    }
}
