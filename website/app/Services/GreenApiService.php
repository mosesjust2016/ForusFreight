<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GreenApiService
{
    protected string $baseUrl;
    protected string $instanceId;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl    = config('services.green_api.base_url', 'https://api.green-api.com');
        $this->instanceId = config('services.green_api.instance_id', '');
        $this->token      = config('services.green_api.token', '');
    }

    /**
     * Send a WhatsApp text message to a phone number.
     *
     * @param string $phone E.164 format or local Zambian format
     * @param string $message
     * @return array ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function sendMessage(string $phone, string $message): array
    {
        $phone = $this->normalisePhone($phone);

        if (empty($this->instanceId) || empty($this->token)) {
            Log::error('Green API: instance_id or token not configured.');
            return ['success' => false, 'message' => 'Green API not configured.', 'data' => null];
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/sendMessage/{$this->token}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'chatId'  => $phone,
                'message' => $message,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['idMessage'])) {
                Log::info('Green API WhatsApp sent', [
                    'to'         => $phone,
                    'message_id' => $body['idMessage'],
                ]);
                return [
                    'success' => true,
                    'message' => 'WhatsApp message sent successfully.',
                    'data'    => $body,
                ];
            }

            Log::error('Green API sendMessage error', [
                'status' => $response->status(),
                'body'   => $body,
                'phone'  => $phone,
            ]);

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Failed to send WhatsApp message.',
                'data'    => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Green API sendMessage exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    /**
     * Receive incoming notification (single message) from the queue.
     *
     * @return array|null
     */
    public function receiveNotification(): ?array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return null;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/receiveNotification/{$this->token}";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $body = $response->json();
                if (!empty($body)) {
                    return $body;
                }
            }
        } catch (\Exception $e) {
            Log::error('Green API receiveNotification exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Delete a received notification from the queue by receiptId.
     */
    public function deleteNotification(int $receiptId): bool
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return false;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/deleteNotification/{$this->token}/{$receiptId}";

        try {
            $response = Http::delete($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Green API deleteNotification exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the QR code URL for initial account authorization.
     *
     * Green API provides a direct web page at qr.green-api.com that renders
     * the QR code automatically and refreshes it. We return that URL so the
     * frontend can show it in an iframe or link.
     *
     * @return array ['success' => bool, 'qr' => ?string, 'message' => string]
     */
    public function getQrCode(): array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return ['success' => false, 'qr' => null, 'message' => 'Green API not configured.'];
        }

        // Direct QR page provided by Green API
        $qrUrl = "https://qr.green-api.com/{$this->instanceId}/{$this->token}";

        return [
            'success' => true,
            'qr'      => $qrUrl,
            'message' => 'Open the QR page and scan with WhatsApp Business to authorize.',
        ];
    }

    /**
     * Logout the WhatsApp account.
     */
    public function logout(): bool
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return false;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/logout/{$this->token}";

        try {
            $response = Http::get($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Green API logout exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get account state (authorized / unauthorized etc.)
     */
    public function getState(): array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return ['success' => false, 'state' => 'not_configured', 'message' => 'Green API not configured.'];
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/getStateInstance/{$this->token}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'state'   => $body['stateInstance'] ?? 'unknown',
                    'message' => $body['message'] ?? 'State retrieved.',
                ];
            }

            return ['success' => false, 'state' => 'error', 'message' => 'Failed to fetch state.'];
        } catch (\Exception $e) {
            Log::error('Green API getState exception: ' . $e->getMessage());
            return ['success' => false, 'state' => 'exception', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check if a phone number has a WhatsApp account.
     *
     * @param string $phone
     * @return array ['exists' => bool, 'message' => string]
     */
    public function checkWhatsApp(string $phone): array
    {
        $phone = $this->normalisePhone($phone);

        if (empty($this->instanceId) || empty($this->token)) {
            return ['exists' => false, 'message' => 'Green API not configured.'];
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/checkWhatsapp/{$this->token}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'phoneNumber' => str_replace('@c.us', '', $phone),
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['existsWhatsapp'])) {
                return [
                    'exists'  => (bool) $body['existsWhatsapp'],
                    'message' => $body['existsWhatsapp'] ? 'Number has WhatsApp.' : 'Number does NOT have WhatsApp.',
                ];
            }

            return ['exists' => false, 'message' => 'Could not verify number.'];
        } catch (\Exception $e) {
            Log::error('Green API checkWhatsApp exception: ' . $e->getMessage());
            return ['exists' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Clear the outgoing message queue.
     */
    public function clearMessagesQueue(): bool
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return false;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/clearMessagesQueue/{$this->token}";

        try {
            $response = Http::get($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Green API clearMessagesQueue exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Show the outgoing message queue.
     */
    public function showMessagesQueue(): array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return ['success' => false, 'queue' => [], 'message' => 'Not configured.'];
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/showMessagesQueue/{$this->token}";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return ['success' => true, 'queue' => $response->json() ?? [], 'message' => 'Queue retrieved.'];
            }
            return ['success' => false, 'queue' => [], 'message' => 'Failed to retrieve queue.'];
        } catch (\Exception $e) {
            Log::error('Green API showMessagesQueue exception: ' . $e->getMessage());
            return ['success' => false, 'queue' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * Get current instance settings.
     */
    public function getSettings(): array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return ['success' => false, 'settings' => [], 'message' => 'Not configured.'];
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/getSettings/{$this->token}";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return ['success' => true, 'settings' => $response->json() ?? [], 'message' => 'Settings retrieved.'];
            }
            return ['success' => false, 'settings' => [], 'message' => 'Failed to retrieve settings.'];
        } catch (\Exception $e) {
            Log::error('Green API getSettings exception: ' . $e->getMessage());
            return ['success' => false, 'settings' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * Set instance settings (e.g., message sending delay).
     */
    public function setSettings(array $settings): bool
    {
        if (empty($this->instanceId) || empty($this->token)) {
            return false;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/setSettings/{$this->token}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $settings);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Green API setSettings exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalise a phone number to Green API chatId format (XXXXXXXXXXX@c.us).
     */
    protected function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If it starts with 0 and is 10 digits (Zambian local), convert to +260
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '260' . substr($phone, 1);
        }

        // Ensure it has no leading + for chatId
        $phone = ltrim($phone, '+');

        return $phone . '@c.us';
    }
}
