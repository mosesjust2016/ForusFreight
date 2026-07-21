<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
    <div style="max-width:500px;margin:0 auto;background:#fff;border-radius:12px;padding:30px;">
        <h2 style="color:#d32f2f;margin-top:0;">{{ $level }} Alert</h2>
        <p style="color:#555;">{{ $appName }} System Notification</p>
        <pre style="background:#f5f5f5;padding:16px;border-radius:8px;font-size:13px;white-space:pre-wrap;">{{ $message }}</pre>
        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p style="color:#999;font-size:12px;">Sent at {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
