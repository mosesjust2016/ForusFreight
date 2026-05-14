<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;
    protected string $senderId;

    public function __construct()
    {
        $this->apiKey    = config('services.zamtel.api_key', '');
        $this->senderId  = config('services.zamtel.sender_id', 'ForusFreigh');
    }

    /**
     * Send an SMS message via Zamtel Bulk SMS.
     */
    public function send(string $to, string $message): bool
    {
        // Normalise Zambian numbers: 07xxxxxxxx -> 2607xxxxxxxx (no + for URL paths)
        $to = ltrim($this->normalisePhone($to), '+');

        // URL encode the message to safely pass it in the URL path
        $encodedMessage = urlencode($message);
        
        $url = "https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/{$this->apiKey}/contacts/{$to}/senderId/{$this->senderId}/message/{$encodedMessage}";

        try {
            // Zamtel uses a GET request with parameters in the URL path
            $response = Http::get($url);

            if ($response->successful()) {
                Log::info("SMS sent to {$to} via Zamtel");
                return true;
            } else {
                Log::error("Zamtel SMS API error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Zamtel SMS send exception: " . $e->getMessage());
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
     * Normalise a phone number to international format.
     */
    protected function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        // Already international
        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Zambian local: 07xxxxxxxx or 09xxxxxxxx
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '+260' . substr($phone, 1);
        }

        // Already has country code without +
        if (str_starts_with($phone, '260')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
