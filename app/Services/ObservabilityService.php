<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ObservabilityService
{
    protected static $adminEmail = 'mjjustme26@gmail.com';

    public static function pushAlert($message, $level = 'INFO', $context = [])
    {
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

        Log::channel('single')->log($psrLevel, $message, $context);

        try {
            app(BrevoMailService::class)->sendSecurityAlert(
                self::$adminEmail,
                $message,
                $level
            );
        } catch (\Exception $e) {
            Log::error('Failed to send observability email: ' . $e->getMessage());
        }

        self::pushWhatsApp($message, $level);
    }

    public static function criticalFailure($error)
    {
        self::pushAlert("URGENT SYSTEM FAILURE DETECTED\n\n" . $error, 'CRITICAL');
    }

    public static function intrusionDetected($details)
    {
        self::pushAlert("POSSIBLE SECURITY INTRUSION DETECTED\n\n" . $details, 'INTRUSION');
    }

    public static function highValueTransaction($type, $amount, $details)
    {
        if ($amount >= 5000) {
            self::pushAlert(
                "HIGH-VALUE FINANCIAL EVENT ($amount ZMW)\n\nType: $type\nDetails: $details",
                'FINANCIAL'
            );
        }
    }

    protected static function pushWhatsApp($message, $level)
    {
        Log::info("[Observability] WhatsApp provision triggered for: $level");
    }
}
