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
        .status-card { background: white; border-left: 5px solid rgb(0, 127, 127); padding: 20px; margin: 20px 0; border-radius: 5px; }
        .new-status { font-size: 32px; font-weight: bold; color: rgb(0, 127, 127); text-align: center; margin: 20px 0; }
        .timeline { margin: 20px 0; }
        .timeline-item { padding-left: 30px; margin: 10px 0; position: relative; }
        .timeline-item:before { content: '✓'; position: absolute; left: 0; color: rgb(0, 127, 127); font-weight: bold; }
        .button { display: inline-block; background: rgb(0, 127, 127); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px auto; display: block; width: fit-content; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
        .detail { display: flex; justify-content: space-between; margin: 8px 0; }
        .detail strong { color: rgb(0, 127, 127); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $statusEmoji }} Shipment Update</h1>
            <p>Your shipment status has been updated</p>
        </div>

        <div class="content">
            <p>Hello {{ $customerName }},</p>

            <p>Great news! Your shipment has been updated.</p>

            <div class="status-card">
                <h3 style="margin-top: 0; color: rgb(0, 127, 127);">Current Status</h3>
                <div class="new-status">{{ $newStatus }}</div>
                <div class="detail">
                    <strong>Tracking Number:</strong>
                    <span style="font-family: monospace; font-weight: bold;">{{ $trackingNumber }}</span>
                </div>
                <div class="detail">
                    <strong>From:</strong>
                    <span>{{ $origin }}</span>
                </div>
                <div class="detail">
                    <strong>To:</strong>
                    <span>{{ $destination }}</span>
                </div>
                @if($estimatedDelivery)
                <div class="detail">
                    <strong>Est. Delivery:</strong>
                    <span>{{ $estimatedDelivery->format('F d, Y') }}</span>
                </div>
                @endif
            </div>

            <div class="status-card">
                <h3 style="margin-top: 0; color: rgb(0, 127, 127);">Journey Timeline</h3>
                <div class="timeline">
                    @switch($newStatus)
                        @case('Order Placed')
                            <div class="timeline-item">Shipment Created</div>
                            @break
                        @case('Pending')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Awaiting Pickup</div>
                            @break
                        @case('In Transit')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Picked Up</div>
                            <div class="timeline-item">In Transit</div>
                            @break
                        @case('At Border')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Picked Up</div>
                            <div class="timeline-item">In Transit</div>
                            <div class="timeline-item">At Border Checkpoint</div>
                            @break
                        @case('Cleared')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Picked Up</div>
                            <div class="timeline-item">In Transit</div>
                            <div class="timeline-item">At Border Checkpoint</div>
                            <div class="timeline-item">Cleared & Approved</div>
                            @break
                        @case('Out for Delivery')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Picked Up</div>
                            <div class="timeline-item">In Transit</div>
                            <div class="timeline-item">At Border Checkpoint</div>
                            <div class="timeline-item">Cleared & Approved</div>
                            <div class="timeline-item">Out for Delivery</div>
                            @break
                        @case('Delivered')
                            <div class="timeline-item">Shipment Created</div>
                            <div class="timeline-item">Picked Up</div>
                            <div class="timeline-item">In Transit</div>
                            <div class="timeline-item">At Border Checkpoint</div>
                            <div class="timeline-item">Cleared & Approved</div>
                            <div class="timeline-item">Out for Delivery</div>
                            <div class="timeline-item" style="color: green; font-weight: bold;">Delivered ✓</div>
                            @break
                        @case('Cancelled')
                            <div class="timeline-item" style="color: red; text-decoration: line-through;">Shipment Cancelled</div>
                            @break
                    @endswitch
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="button">View Full Tracking Details</a>
            </div>

            <p style="background: #e8f5f5; padding: 15px; border-radius: 5px; border-left: 4px solid rgb(0, 127, 127);">
                <strong>💡 Tip:</strong> Save your tracking number <strong>{{ $trackingNumber }}</strong> for future reference. You can track your shipment anytime at <a href="{{ $trackingUrl }}" style="color: rgb(0, 127, 127);">forusfreight.com/track</a>
            </p>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
                Questions? Contact our support team at <strong>+260572788685</strong> (WhatsApp)<br>
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
