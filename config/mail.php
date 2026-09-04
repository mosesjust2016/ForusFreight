<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | All transactional emails are sent via BrevoMailService (Brevo API).
    | This mailer is only used as a fallback/safety net if anything falls
    | through to Laravel's Mail system. Set to 'log' so missed emails are
    | logged rather than silently lost.
    |
    */

    'default' => env('MAIL_MAILER', 'brevo'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Kept minimal — email delivery is handled by BrevoMailService, not
    | Laravel's mail transports.
    |
    */

    'mailers' => [

        'brevo' => [
            'transport' => 'smtp',
            'host'      => env('BREVO_SMTP_HOST', 'smtp-relay.brevo.com'),
            'port'      => env('BREVO_SMTP_PORT', 587),
            'encryption' => env('BREVO_SMTP_ENCRYPTION', 'tls'),
            'username'  => env('BREVO_SMTP_LOGIN'),
            'password'  => env('BREVO_SMTP_KEY'),
            'timeout'   => 15,
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@forusfl.co.zm'),
        'name' => env('MAIL_FROM_NAME', 'Forus Freight'),
    ],

];
