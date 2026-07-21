<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:40px;text-align:center;">
        <h2 style="color:#1a1a1a;margin-bottom:8px;">{{ config('app.name') }}</h2>
        <p style="color:#555;margin-bottom:32px;">Hi {{ $name }}, verify your email address.</p>
        <div style="background:#f0faf7;border:2dashed #007f7f;border-radius:10px;padding:24px;margin-bottom:32px;">
            <p style="margin:0 0 6px;font-size:13px;color:#555;letter-spacing:1px;text-transform:uppercase;">Your verification code</p>
            <span style="font-size:36px;font-weight:700;letter-spacing:10px;color:#007f7f;">{{ $otp }}</span>
        </div>
        <p style="color:#888;font-size:13px;">This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
        <p style="color:#bbb;font-size:12px;">If you didn't create an account, you can safely ignore this email.</p>
    </div>
</body>
</html>
