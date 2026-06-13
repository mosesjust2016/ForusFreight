<?php

namespace App\Observers;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Mail\ShipmentCreatedNotification;
use App\Mail\ShipmentStatusUpdatedNotification;
use App\Services\BrevoMailService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Mail;

class ShipmentObserver
{
    /**
     * Handle the Shipment "created" event.
     */
    public function created(Shipment $shipment): void
    {
        // Send email notification to customer
        try {
            Mail::send(new ShipmentCreatedNotification($shipment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send shipment created email', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Create initial tracking event
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $shipment->status,
            'description' => 'Shipment created and confirmed',
            'location' => $shipment->origin ?? 'Origin',
            'event_time' => now(),
        ]);
    }

    /**
     * Handle the Shipment "updated" event.
     */
    public function updated(Shipment $shipment): void
    {
        // Check if status changed
        if ($shipment->wasChanged('status')) {
            $oldStatus = $shipment->getOriginal('status');
            $newStatus = $shipment->status;

            // Create tracking event for status change
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $newStatus,
                'description' => "Status changed from '{$oldStatus}' to '{$newStatus}'",
                'location' => $shipment->origin ?? 'In Transit',
                'event_time' => now(),
            ]);

            // Send email notification to customer
            try {
                Mail::send(new ShipmentStatusUpdatedNotification($shipment, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send shipment status updated email', [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send SMS notification to customer (optional - can be toggled via preferences)
            try {
                if ($shipment->user && $shipment->user->phone) {
                    $message = "Your Forus Freight shipment {$shipment->tracking_number} has been {$newStatus}. Track: https://forusfreight.com/tracking";
                    app(SmsService::class)->sendShipmentUpdate($shipment->user->phone, $message);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send shipment status SMS', [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Shipment "deleted" event.
     */
    public function deleted(Shipment $shipment): void
    {
        // Optionally log deletion
        \Illuminate\Support\Facades\Log::info('Shipment deleted', [
            'shipment_id' => $shipment->id,
            'tracking_number' => $shipment->tracking_number,
        ]);
    }
}
