<?php

namespace App\Notifications;

use App\Services\BrevoMailService;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPasswordNotification extends BaseResetPassword
{
    public function send($notifiable, $channel)
    {
        $url = $this->resetUrl($notifiable);

        $html = view('emails.reset-password', [
            'name' => $notifiable->name ?? 'User',
            'url'  => $url,
        ])->render();

        app(BrevoMailService::class)->send(
            $notifiable->email,
            $notifiable->name ?? 'User',
            'Reset Password - ' . config('app.name'),
            $html
        );
    }
}
