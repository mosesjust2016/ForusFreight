<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BrevoMailService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct()
    {
        $this->apiKey = config('services.brevo.key');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlContent, string $textContent = ''): bool
    {
        // Try Brevo HTTP API first
        if ($this->isConfigured()) {
            try {
                $response = Http::timeout(15)->withHeaders([
                    'api-key'      => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl, [
                    'sender' => [
                        'name'  => config('services.brevo.sender_name'),
                        'email' => config('services.brevo.sender_email'),
                    ],
                    'to' => [
                        ['email' => $toEmail, 'name' => $toName],
                    ],
                    'subject'     => $subject,
                    'htmlContent' => $htmlContent,
                    'textContent' => $textContent ?: strip_tags($htmlContent),
                ]);

                if ($response->successful()) {
                    return true;
                }

                Log::warning('Brevo HTTP API failed, falling back to SMTP', [
                    'status'   => $response->status(),
                    'response' => $response->json(),
                    'email'    => $toEmail,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Brevo HTTP API exception, falling back to SMTP', [
                    'error' => $e->getMessage(),
                    'email' => $toEmail,
                ]);
            }
        }

        // Fallback: Laravel Mail via Brevo SMTP relay
        return $this->sendViaSmtp($toEmail, $toName, $subject, $htmlContent, $textContent);
    }

    private function sendViaSmtp(string $toEmail, string $toName, string $subject, string $htmlContent, string $textContent = ''): bool
    {
        $smtpLogin = config('services.brevo.smtp_login');
        $smtpKey   = config('services.brevo.smtp_key');

        if (empty($smtpLogin) || empty($smtpKey)) {
            Log::error('Brevo SMTP fallback not configured — no SMTP login or key', ['email' => $toEmail]);
            return false;
        }

        try {
            $plainContent = $textContent ?: strip_tags($htmlContent);

            Mail::raw($plainContent, function ($message) use ($toEmail, $toName, $subject, $htmlContent) {
                $message->to($toEmail, $toName)
                    ->subject($subject)
                    ->html($htmlContent);
            });

            Log::info('Email sent via Brevo SMTP fallback', ['email' => $toEmail, 'subject' => $subject]);
            return true;
        } catch (\Throwable $e) {
            Log::error('Brevo SMTP fallback also failed', [
                'error' => $e->getMessage(),
                'email' => $toEmail,
            ]);
            return false;
        }
    }

    /* ─── OTP Email ────────────────────────────────────────── */

    public function sendOtpEmail(string $toEmail, string $toName, string $otp): bool
    {
        $html = view('emails.otp', [
            'name' => $toName,
            'otp'  => $otp,
        ])->render();

        return $this->send($toEmail, $toName, 'Your Email Verification Code', $html);
    }

    /* ─── Shipment Emails ──────────────────────────────────── */

    public function sendShipmentCreated(Shipment $shipment): bool
    {
        $user = $shipment->user;
        if (!$user || !$user->email) return false;

        $html = view('emails.shipment-created', [
            'shipment'         => $shipment,
            'customerName'     => $user->name ?? 'Valued Customer',
            'trackingNumber'   => $shipment->serial_no,
            'origin'           => $shipment->origin,
            'destination'      => $shipment->destination,
            'status'           => $shipment->status,
            'weight'           => $shipment->weight,
            'estimatedDelivery'=> $shipment->estimated_delivery,
            'cost'             => $shipment->cost,
            'trackingUrl'      => url("/track?serial_no={$shipment->serial_no}"),
        ])->render();

        return $this->send(
            $user->email,
            $user->name ?? 'Valued Customer',
            "Shipment Confirmation - {$shipment->serial_no}",
            $html
        );
    }

    public function sendShipmentUpdated(Shipment $shipment, string $oldStatus, string $newStatus): bool
    {
        $user = $shipment->user;
        if (!$user || !$user->email) return false;

        $statusEmoji = match($newStatus) {
            'Delivered'       => '✅',
            'In Transit'      => '🚚',
            'At Border'       => '📍',
            'Cleared'         => '✓',
            'Out for Delivery'=> '📦',
            'Cancelled'       => '❌',
            default           => '📋',
        };

        $html = view('emails.shipment-status-updated', [
            'shipment'         => $shipment,
            'customerName'     => $user->name ?? 'Valued Customer',
            'trackingNumber'   => $shipment->serial_no,
            'oldStatus'        => $oldStatus,
            'newStatus'        => $newStatus,
            'statusEmoji'      => $statusEmoji,
            'origin'           => $shipment->origin,
            'destination'      => $shipment->destination,
            'estimatedDelivery'=> $shipment->estimated_delivery,
            'trackingUrl'      => url("/track?serial_no={$shipment->serial_no}"),
            'dashboardUrl'     => url("/dashboard"),
        ])->render();

        return $this->send(
            $user->email,
            $user->name ?? 'Valued Customer',
            "Shipment Update - {$newStatus} - {$shipment->serial_no}",
            $html
        );
    }

    /* ─── Security Alert ───────────────────────────────────── */

    public function sendSecurityAlert(string $toEmail, string $message, string $level): bool
    {
        $html = view('emails.security-alert', [
            'message' => $message,
            'level'   => $level,
            'appName' => config('app.name'),
        ])->render();

        return $this->send(
            $toEmail,
            'Admin',
            "[{$level}] " . config('app.name') . " Security Alert",
            $html
        );
    }
}
