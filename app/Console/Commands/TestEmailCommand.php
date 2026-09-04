<?php

namespace App\Console\Commands;

use App\Services\BrevoMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email? : The email address to test with}';
    protected $description = 'Test email configuration by sending a test OTP email';

    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        if (!$email) {
            $this->error('No email address provided and no default configured.');
            return 1;
        }

        $this->info("Testing email to: {$email}");

        $service = app(BrevoMailService::class);

        if (!$service->isConfigured()) {
            $this->error('❌ Brevo email service is not properly configured!');
            $this->line('Please set BREVO_API_KEY in your .env file.');
            return 1;
        }

        $testOtp = '123456';
        $success = $service->sendOtpEmail($email, 'Test User', $testOtp);

        if ($success) {
            $this->info("✅ Email sent successfully to {$email}");
            Log::info('Test email sent successfully', ['to' => $email]);
            return 0;
        } else {
            $this->error("❌ Failed to send email to {$email}");
            $this->line('Check logs for more details: storage/logs/laravel.log');
            return 1;
        }
    }
}
