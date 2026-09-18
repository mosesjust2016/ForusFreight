<?php

namespace App\Services;

use App\Models\CommunicationsLog;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class ShipmentNotifierService
{
    public function __construct(
        protected GreenApiService $greenApi,
        protected SmsService $sms,
    ) {}

    /**
     * Notify a shipment's owner about their shipment.
     *
     * Channel preference: WhatsApp first, SMS as fallback (per business
     * rule). The recipient phone is resolved from the user's verified phone,
     * then the shipment's phone_number, then client_phone. Outside of
     * production every notification is redirected to the configured test
     * phone so a local/dev environment never contacts real clients.
     *
     * @return array ['status' => 'sent'|'failed'|'skipped', 'channel' => ?string, 'phone' => ?string, 'reason' => ?string]
     */
    public function notifyOwner(Shipment $shipment, bool $created = true): array
    {
        try {
            $recipient = $this->resolveRecipientPhone($shipment);
            if (!$recipient) {
                return ['status' => 'skipped', 'channel' => null, 'phone' => null, 'reason' => 'No phone number on file.'];
            }

            if (!app()->environment('production')) {
                $recipient = config('services.notify.test_phone', '+260770826668');
            }

            $message = $this->buildMessage($shipment, $created);
            $outcome = $this->send($recipient, $message);

            return [
                'status'  => $outcome['status'],
                'channel' => $outcome['channel'],
                'phone'   => $this->maskPhone($recipient),
                'reason'  => $outcome['reason'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Shipment owner notification failed', [
                'shipment_id' => $shipment->id,
                'error'       => $e->getMessage(),
            ]);
            return ['status' => 'failed', 'channel' => null, 'phone' => null, 'reason' => $e->getMessage()];
        }
    }

    protected function resolveRecipientPhone(Shipment $shipment): ?string
    {
        $phone = $shipment->user?->phone
            ?: $shipment->phone_number
            ?: $shipment->client_phone;

        $phone = trim((string) $phone);

        return $phone !== '' ? $phone : null;
    }

    protected function buildMessage(Shipment $shipment, bool $created): string
    {
        $baseUrl   = rtrim(config('app.url'), '/');
        $name      = $shipment->user?->name ?: 'there';
        $firstName = trim(explode(' ', $name)[0]);
        $serial    = $shipment->serial_no ?: '-';
        $routeLine = "{$shipment->origin} to {$shipment->destination}";
        $action    = $created ? 'has been registered' : 'has been updated';

        $intro = "Hello {$firstName}, your shipment {$serial} ({$routeLine}) {$action} with Forus Freight.";

        $hasAccount = $shipment->user
            && (!empty($shipment->user->email) || !empty($shipment->user->phone_verified_at));

        if ($hasAccount) {
            return "{$intro}\n\nTrack it anytime: {$baseUrl}/tracking";
        }

        return "{$intro}\n\nTo track your shipment, create a free account: {$baseUrl}/register\nOr track anytime: {$baseUrl}/tracking";
    }

    protected function send(string $phone, string $message): array
    {
        // WhatsApp first...
        $wa = $this->greenApi->checkWhatsApp($phone);
        if ($wa['exists']) {
            $res = $this->greenApi->sendMessage($phone, $message);
            $this->log('whatsapp', $phone, $message, $res['success'] ? 'sent' : 'failed', $res['data'] ?? null);
            $this->throttle();

            if ($res['success']) {
                return ['status' => 'sent', 'channel' => 'whatsapp'];
            }
        }

        // ...SMS fallback (no WhatsApp, could not verify, or WhatsApp failed).
        $smsSent = $this->sms->send($phone, $message);
        $this->log('sms', $phone, $message, $smsSent ? 'sent' : 'failed');
        $this->throttle();

        return ['status' => $smsSent ? 'sent' : 'failed', 'channel' => 'sms'];
    }

    protected function throttle(): void
    {
        // Only meaningful in production where real recipients are contacted.
        if (app()->environment('production')) {
            usleep((int) (config('services.notify.send_delay_ms', 500) * 1000));
        }
    }

    protected function log(string $channel, string $phone, string $message, string $status, $externalId = null): void
    {
        try {
            CommunicationsLog::create([
                'channel'        => $channel,
                'direction'      => 'outgoing',
                'recipient_phone'=> $phone,
                'message'        => $message,
                'status'         => $status,
                'external_id'    => is_array($externalId) ? ($externalId['idMessage'] ?? null) : $externalId,
                'metadata'       => ['purpose' => 'bulk_upload_notification'],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to write communications log', ['error' => $e->getMessage()]);
        }
    }

    protected function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return strlen($digits) >= 6
            ? substr($digits, 0, 2) . '****' . substr($digits, -2)
            : $phone;
    }
}