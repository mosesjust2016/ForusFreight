<?php

namespace App\Services;

use App\Mail\SecurityAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ObservabilityService
{
    protected static $adminEmail = 'mjjustme26@gmail.com';

    /**
     * Centralized alerting method
     */
    public static function pushAlert($message, $level = 'INFO', $context = [])
    {
        // 1. Map custom levels to standard PSR-3 levels for internal logging
        $logLevelMap = [
            'CRITICAL' => 'critical',
            'INTRUSION' => 'alert',
            'SYSTEM_ERROR' => 'error',
            'FINANCIAL' => 'info',
            'SECURITY' => 'notice',
            'INFO' => 'info',
            'ERROR' => 'error'
        ];

        $psrLevel = $logLevelMap[$level] ?? 'info';

        // 2. Log locally using standard level
        Log::channel('single')->log($psrLevel, $message, $context);

        // 3. Email Notification (keeps the rich custom label)
        try {
            Mail::to(self::$adminEmail)->send(new SecurityAlert($message, $level));
        } catch (\Exception $e) {
            Log::error('Failed to send observability email: ' . $e->getMessage());
        }

        // 3. WhatsApp Provision (Placeholder)
        self::pushWhatsApp($message, $level);
    }

    /**
     * Logic for system failures / intrusions
     */
    public static function criticalFailure($error)
    {
        self::pushAlert(
            "URGENT SYSTEM FAILURE DETECTED\n\n" . $error,
            'CRITICAL'
        );
    }

    public static function intrusionDetected($details)
    {
        self::pushAlert(
            "POSSIBLE SECURITY INTRUSION DETECTED\n\n" . $details,
            'INTRUSION'
        );
    }

    /**
     * Logic for financial alerts
     */
    public static function highValueTransaction($type, $amount, $details)
    {
        if ($amount >= 5000) {
            self::pushAlert(
                "HIGH-VALUE FINANCIAL EVENT ($amount ZMW)\n\n" .
                "Type: $type\n" .
                "Details: $details",
                'FINANCIAL'
            );
        }
    }

    /**
     * WhatsApp Notification Hook (to be implemented later)
     */
    protected static function pushWhatsApp($message, $level)
    {
        // Provision for future WhatsApp API integration
        // Example: Http::post('your-whatsapp-api-url', [...]);
        
        Log::info("[Observability] WhatsApp provision triggered for: $level");
    }
}
