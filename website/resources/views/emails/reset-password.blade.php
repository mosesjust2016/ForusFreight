<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:40px;text-align:center;">
        <h2 style="color:#1a1a1a;margin-bottom:8px;">{{ config('app.name') }}</h2>
        <p style="color:#555;margin-bottom:32px;">Hi {{ $name }}, we received a request to reset your password.</p>
        <div style="margin-bottom:32px;">
            <a href="{{ $url }}" style="display:inline-block;background:#007f7f;color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:15px;">Reset Password</a>
        </div>
        <p style="color:#888;font-size:13px;">This link expires in <strong>{{ floor(config('auth.passwords.users.expire', 60) / 60) }} hour(s)</strong>.</p>
        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
        <p style="color:#bbb;font-size:12px;">If you didn't request a password reset, you can safely ignore this email.</p>
    </div>
</body>
</html>
