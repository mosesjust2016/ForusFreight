<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:40px;">
        <h2 style="color:#1a1a1a;margin-bottom:8px;text-align:center;">{{ config('app.name') }}</h2>
        <p style="color:#555;margin-bottom:24px;text-align:center;">New Quote Request</p>
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Service Type</td><td style="padding:8px 0;color:#1a1a1a;">{{ $serviceType }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Full Name</td><td style="padding:8px 0;color:#1a1a1a;">{{ $fullName }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Company</td><td style="padding:8px 0;color:#1a1a1a;">{{ $company ?: 'N/A' }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Email</td><td style="padding:8px 0;color:#1a1a1a;">{{ $email }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Phone</td><td style="padding:8px 0;color:#1a1a1a;">{{ $phone }}</td></tr>
        </table>
        <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">
        <p style="color:#007f7f;font-weight:bold;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">Pickup &amp; Delivery</p>
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Pickup</td><td style="padding:8px 0;color:#1a1a1a;">{{ $pickup }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Delivery</td><td style="padding:8px 0;color:#1a1a1a;">{{ $delivery }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Weight</td><td style="padding:8px 0;color:#1a1a1a;">{{ $weight ?: 'Not specified' }}</td></tr>
            <tr><td style="padding:8px 0;color:#888;font-weight:bold;">Dimensions</td><td style="padding:8px 0;color:#1a1a1a;">{{ $dimensions ?: 'Not specified' }}</td></tr>
        </table>
        @if($details)
        <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">
        <p style="color:#007f7f;font-weight:bold;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">Additional Details</p>
        <p style="color:#555;font-size:14px;">{{ $details }}</p>
        @endif
        <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">
        <p style="color:#bbb;font-size:12px;text-align:center;">Submitted on {{ $submittedAt }}</p>
    </div>
</body>
</html>
