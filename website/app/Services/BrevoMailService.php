<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct()
    {
        $this->apiKey = config('services.brevo.key');
    }

    public function sendOtpEmail(string $toEmail, string $toName, string $otp): bool
    {
        $response = Http::withHeaders([
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
            'subject'     => 'Your Email Verification Code',
            'htmlContent' => $this->otpHtml($toName, $otp),
            'textContent' => $this->otpText($toName, $otp),
        ]);

        if ($response->failed()) {
            Log::error('Brevo mail failed', [
                'status'   => $response->status(),
                'response' => $response->json(),
                'email'    => $toEmail,
            ]);
            return false;
        }

        return true;
    }

    private function otpHtml(string $name, string $otp): string
    {
        $appName = config('app.name');

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
          <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:40px;text-align:center;">
            <h2 style="color:#1a1a1a;margin-bottom:8px;">{$appName}</h2>
            <p style="color:#555;margin-bottom:32px;">Hi {$name}, verify your email address.</p>
            <div style="background:#f0faf7;border:2px dashed #007f7f;border-radius:10px;padding:24px;margin-bottom:32px;">
              <p style="margin:0 0 6px;font-size:13px;color:#555;letter-spacing:1px;text-transform:uppercase;">Your verification code</p>
              <span style="font-size:36px;font-weight:700;letter-spacing:10px;color:#007f7f;">{$otp}</span>
            </div>
            <p style="color:#888;font-size:13px;">This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
            <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
            <p style="color:#bbb;font-size:12px;">If you didn't create an account, you can safely ignore this email.</p>
          </div>
        </body>
        </html>
        HTML;
    }

    private function otpText(string $name, string $otp): string
    {
        $appName = config('app.name');

        return "Hi {$name},\n\nYour {$appName} verification code is: {$otp}\n\nThis code expires in 10 minutes. Do not share it with anyone.\n\nIf you didn't create an account, ignore this email.";
    }
}
