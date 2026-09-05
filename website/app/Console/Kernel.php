<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Processes the database queue (WhatsApp campaign sends and any future
        // queued jobs). Shared hosting generally can't run a persistent
        // `queue:work` daemon, so this runs it in short, bounded bursts via
        // cron instead — the standard pattern for that environment.
        $schedule->command('queue:work --stop-when-empty --max-time=50 --tries=3')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('whatsapp:poll-incoming')->everyMinute();
        $schedule->command('whatsapp:resolve-ab-winners')->everyFifteenMinutes();
        $schedule->command('whatsapp:check-campaign-alerts')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}