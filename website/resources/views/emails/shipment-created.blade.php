<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, rgb(0, 127, 127), rgb(255, 98, 0)); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .section { margin: 20px 0; padding: 15px; background: white; border-left: 4px solid rgb(0, 127, 127); }
        .section h3 { margin-top: 0; color: rgb(0, 127, 127); }
        .detail { display: flex; justify-content: space-between; margin: 10px 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .detail strong { color: rgb(0, 127, 127); }
        .button { display: inline-block; background: rgb(0, 127, 127); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
        .status-badge { display: inline-block; background: rgb(255, 98, 0); color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Shipment Confirmed</h1>
            <p>Your shipment has been successfully created</p>
        </div>

        <div class="content">
            <p>Hello {{ $customerName }},</p>

            <p>Thank you for choosing Forus Freight! Your shipment has been confirmed and is ready for dispatch.</p>

            <div class="section">
                <h3>Shipment Details</h3>
                <div class="detail">
                    <strong>Tracking Number:</strong>
                    <span style="font-family: monospace; font-weight: bold;">{{ $trackingNumber }}</span>
                </div>
                <div class="detail">
                    <strong>Status:</strong>
                    <span class="status-badge">{{ $status }}</span>
                </div>
                <div class="detail">
                    <strong>From:</strong>
                    <span>{{ $origin }}</span>
                </div>
                <div class="detail">
                    <strong>To:</strong>
                    <span>{{ $destination }}</span>
                </div>
                <div class="detail">
                    <strong>Weight:</strong>
                    <span>{{ $weight ?? 'N/A' }} kg</span>
                </div>
                @if($estimatedDelivery)
                <div class="detail">
                    <strong>Est. Delivery:</strong>
                    <span>{{ $estimatedDelivery->format('F d, Y') }}</span>
                </div>
                @endif
                @if($cost)
                <div class="detail">
                    <strong>Cost:</strong>
                    <span>ZMW {{ number_format($cost, 2) }}</span>
                </div>
                @endif
            </div>

            <div class="section">
                <h3>📦 What's Next?</h3>
                <ol>
                    <li>Your shipment will be picked up from the origin location</li>
                    <li>Transit through our network</li>
                    <li>Customs clearance (if applicable)</li>
                    <li>Out for delivery</li>
                    <li>Delivery to your destination</li>
                </ol>
            </div>

            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="button">Track Your Shipment</a>
            </div>

            <p>You'll receive updates at each stage. For real-time tracking, visit the link above or use your tracking number: <strong>{{ $trackingNumber }}</strong></p>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
                Need help? Contact our support team at <strong>+260572788685</strong> (WhatsApp)<br>
                or visit <a href="https://forusfreight.com" style="color: rgb(0, 127, 127);">forusfreight.com</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Forus Freight Ltd. All rights reserved.</p>
            <p>Kafure Road, Lusaka, Zambia</p>
        </div>
    </div>
</body>
</html>
