<?php

namespace App\Mail;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentCreatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Shipment $shipment)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Shipment Confirmation - Tracking #{$this->shipment->tracking_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.shipment-created',
            with: [
                'shipment' => $this->shipment,
                'customerName' => $this->shipment->user->name ?? 'Valued Customer',
                'trackingNumber' => $this->shipment->tracking_number,
                'origin' => $this->shipment->origin,
                'destination' => $this->shipment->destination,
                'status' => $this->shipment->status,
                'weight' => $this->shipment->weight,
                'estimatedDelivery' => $this->shipment->estimated_delivery,
                'cost' => $this->shipment->cost,
                'trackingUrl' => url("/track?number={$this->shipment->tracking_number}"),
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
