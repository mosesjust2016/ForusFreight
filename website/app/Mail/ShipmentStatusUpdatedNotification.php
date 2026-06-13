<?php

namespace App\Mail;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusUpdatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Shipment $shipment,
        public string $oldStatus,
        public string $newStatus
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Shipment Update - {$this->newStatus} - #{$this->shipment->tracking_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $statusEmoji = match($this->newStatus) {
            'Delivered' => '✅',
            'In Transit' => '🚚',
            'At Border' => '📍',
            'Cleared' => '✓',
            'Out for Delivery' => '📦',
            'Cancelled' => '❌',
            default => '📋',
        };

        return new Content(
            view: 'emails.shipment-status-updated',
            with: [
                'shipment' => $this->shipment,
                'customerName' => $this->shipment->user->name ?? 'Valued Customer',
                'trackingNumber' => $this->shipment->tracking_number,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'statusEmoji' => $statusEmoji,
                'origin' => $this->shipment->origin,
                'destination' => $this->shipment->destination,
                'estimatedDelivery' => $this->shipment->estimated_delivery,
                'trackingUrl' => url("/track?number={$this->shipment->tracking_number}"),
                'dashboardUrl' => url("/dashboard"),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
