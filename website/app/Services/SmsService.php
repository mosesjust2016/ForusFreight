<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $email;
    protected string $password;
    protected string $originator;
    protected string $baseUrl;

    // Cache keys
    private const ACCESS_TOKEN_KEY  = 'molo_access_token';
    private const REFRESH_TOKEN_KEY = 'molo_refresh_token';

    public function __construct()
    {
        $this->email      = config('services.molo.email', '');
        $this->password   = config('services.molo.password', '');
        $this->originator = config('services.molo.originator', 'FORUSFL');
        $this->baseUrl    = config('services.molo.url', 'https://api.molomarketing.cloud');
    }

    /**
     * Send an SMS via Molo Marketing Cloud.
     */
    public function send(string $to, string $message): bool
    {
        $to    = $this->normalisePhone($to);
        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('Molo SMS: could not obtain a valid access token.');
            return false;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/sms/send", [
                    'destination' => $to,
                    'message'     => $message,
                    'originator'  => $this->originator,
                    'msg_type'    => 0,
                    'dlr'         => 1,
                ]);

            $body = $response->json();

            if ($response->successful() && isset($body['isError']) && $body['isError'] === false) {
                Log::info('Molo SMS sent', [
                    'to'         => $to,
                    'message_id' => $body['data']['provider_message_id'] ?? null,
                    'status'     => $body['data']['status'] ?? null,
                ]);
                return true;
            }

            // Token may have just expired mid-flight — clear cache and retry once
            if ($response->status() === 401) {
                Cache::forget(self::ACCESS_TOKEN_KEY);
                $token = $this->getAccessToken();

                if ($token) {
                    return $this->send($to, $message);
                }
            }

            Log::error('Molo SMS API error', ['status' => $response->status(), 'body' => $body]);
        } catch (\Exception $e) {
            Log::error('Molo SMS exception: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Send the OTP verification message.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "Your Forus Freight verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        return $this->send($phone, $message);
    }

    /**
     * Send a shipment status update notification.
     */
    public function sendShipmentUpdate(string $phone, string $message): bool
    {
        return $this->send($phone, $message);
    }

    // -------------------------------------------------------------------------
    // Token lifecycle
    // -------------------------------------------------------------------------

    /**
     * Return a valid access token, refreshing or re-authenticating as needed.
     */
    private function getAccessToken(): ?string
    {
        // 1. Use cached access token if still valid
        $accessToken = Cache::get(self::ACCESS_TOKEN_KEY);
        if ($accessToken) {
            return $accessToken;
        }

        // 2. Try refresh token
        $refreshToken = Cache::get(self::REFRESH_TOKEN_KEY);
        if ($refreshToken) {
            $accessToken = $this->refreshAccessToken($refreshToken);
            if ($accessToken) {
                return $accessToken;
            }
        }

        // 3. Full login with credentials
        return $this->login();
    }

    /**
     * Authenticate with email + password and cache both tokens.
     */
    private function login(): ?string
    {
        try {
            $response = Http::acceptJson()->post("{$this->baseUrl}/api/auth/login", [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['isError']) && $body['isError'] === false) {
                $accessToken  = $body['data']['access_token']  ?? null;
                $refreshToken = $body['data']['refresh_token'] ?? null;

                if ($accessToken) {
                    $this->cacheToken(self::ACCESS_TOKEN_KEY, $accessToken, 60);
                }
                if ($refreshToken) {
                    $this->cacheToken(self::REFRESH_TOKEN_KEY, $refreshToken, 60 * 24 * 30);
                }

                Log::info('Molo SMS: logged in and obtained new tokens.');
                return $accessToken;
            }

            Log::error('Molo SMS login failed', ['body' => $body]);
        } catch (\Exception $e) {
            Log::error('Molo SMS login exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Use the refresh token to obtain a new access token.
     */
    private function refreshAccessToken(string $refreshToken): ?string
    {
        try {
            $response = Http::withToken($refreshToken)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/auth/refresh");

            $body = $response->json();

            if ($response->successful() && isset($body['isError']) && $body['isError'] === false) {
                $accessToken = $body['data']['access_token'] ?? null;

                if ($accessToken) {
                    $this->cacheToken(self::ACCESS_TOKEN_KEY, $accessToken, 60);
                    Log::info('Molo SMS: access token refreshed.');
                    return $accessToken;
                }
            }

            // Refresh token itself has expired — clear it so next call does a full login
            Cache::forget(self::REFRESH_TOKEN_KEY);
            Log::warning('Molo SMS: refresh token expired, will re-login.');
        } catch (\Exception $e) {
            Log::error('Molo SMS refresh exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cache a token, expiring slightly before its JWT exp claim to avoid edge-case races.
     * Falls back to $defaultMinutes if the JWT cannot be decoded.
     */
    private function cacheToken(string $key, string $token, int $defaultMinutes): void
    {
        $ttlSeconds = $this->jwtTtlSeconds($token) ?? ($defaultMinutes * 60);

        // Keep a 60-second safety buffer so we refresh before the server rejects it
        $ttlSeconds = max($ttlSeconds - 60, 30);

        Cache::put($key, $token, now()->addSeconds($ttlSeconds));
    }

    /**
     * Decode the JWT payload and return seconds until expiry, or null on failure.
     */
    private function jwtTtlSeconds(string $token): ?int
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!isset($payload['exp'])) {
            return null;
        }

        $ttl = (int) $payload['exp'] - time();
        return $ttl > 0 ? $ttl : null;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Normalise a phone number to E.164 format (+260XXXXXXXXX).
     */
    protected function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '+260' . substr($phone, 1);
        }

        if (str_starts_with($phone, '260')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
