<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;
    public $level;

    public function __construct($messageText, $level = 'CRITICAL')
    {
        $this->messageText = $messageText;
        $this->level = $level;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->level . '] Forus Freight System Security Alert',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>System Security Alert</h2>
                <p><strong>Level:</strong> {$this->level}</p>
                <p><strong>Timestamp:</strong> " . now()->toDateTimeString() . "</p>
                <hr>
                <p>{$this->messageText}</p>
                <hr>
                <p>This is an automated observability alert from the Forus Freight Backend.</p>
            ",
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
