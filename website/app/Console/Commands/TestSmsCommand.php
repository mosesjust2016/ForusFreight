<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSmsCommand extends Command
{
    protected $signature = 'sms:test {phone : The phone number to test with (e.g., +260961234567)}';
    protected $description = 'Test SMS configuration by sending a test OTP message';

    public function handle()
    {
        $phone = $this->argument('phone');

        if (!$phone) {
            $this->error('Phone number is required.');
            return 1;
        }

        $this->info("Testing SMS to: {$phone}");

        $service = app(SmsService::class);

        if (!$service->isConfigured()) {
            $this->warn('⚠️  Molo SMS service is not properly configured.');
            $this->line('Please set MOLO_EMAIL and MOLO_PASSWORD in your .env file.');
            $this->line('For now, SMS messages will be logged but not sent.');
            return 1;
        }

        $testOtp = '123456';
        $success = $service->sendOtp($phone, $testOtp);

        if ($success) {
            $this->info("✅ SMS sent successfully to {$phone}");
            Log::info('Test SMS sent successfully', ['to' => $phone]);
            return 0;
        } else {
            $this->error("❌ Failed to send SMS to {$phone}");
            $this->line('Check logs for more details: storage/logs/laravel.log');
            return 1;
        }
    }
}
