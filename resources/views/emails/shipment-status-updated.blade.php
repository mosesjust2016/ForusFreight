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
                    @php $canonicalStatus = \App\Models\Shipment::canonicalStatus($newStatus); @endphp
                    {{-- Stages before the current one --}}
                    <div class="timeline-item">Shipment Created</div>
                    @if (in_array($canonicalStatus, ['AWAITING_RECEIPT', 'RECEIVED_CN', 'PROCESSING', 'CONSOLIDATED', 'READY_TO_SHIP', 'DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Awaiting Arrival at China Warehouse</div>
                    @endif
                    @if (in_array($canonicalStatus, ['RECEIVED_CN', 'PROCESSING', 'CONSOLIDATED', 'READY_TO_SHIP', 'DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Received at China Warehouse</div>
                    @endif
                    @if (in_array($canonicalStatus, ['PROCESSING', 'CONSOLIDATED', 'READY_TO_SHIP', 'DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Shipment Being Processed &amp; Consolidated</div>
                    @endif
                    @if (in_array($canonicalStatus, ['READY_TO_SHIP', 'DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Ready for Shipment</div>
                    @endif
                    @if (in_array($canonicalStatus, ['DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Departed China</div>
                    @endif
                    @if (in_array($canonicalStatus, ['IN_TRANSIT', 'ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">In Transit to Zambia</div>
                    @endif
                    @if (in_array($canonicalStatus, ['ARRIVED_ZM', 'CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Arrived in Zambia</div>
                    @endif
                    @if (in_array($canonicalStatus, ['CUSTOMS_CLEARANCE', 'CLEARED', 'READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        <div class="timeline-item">Customs Clearance</div>
                    @endif
                    @if (in_array($canonicalStatus, ['READY_FOR_COLLECTION', 'OUT_FOR_DELIVERY', 'DELIVERED'], true))
                        @if (in_array($canonicalStatus, ['READY_FOR_COLLECTION'], true))
                            <div class="timeline-item">Ready for Collection</div>
                        @endif
                        <div class="timeline-item">Out for Delivery</div>
                    @endif
                    @if ($canonicalStatus === 'DELIVERED')
                        <div class="timeline-item" style="color: green; font-weight: bold;">Delivered ✓</div>
                    @endif
                    @if (in_array($canonicalStatus, ['EXCEPTION'], true))
                        <div class="timeline-item" style="color: red;">Shipment Exception</div>
                    @endif
                    @if (in_array($canonicalStatus, ['ON_HOLD'], true))
                        <div class="timeline-item" style="color: #b45309;">Shipment on Hold</div>
                    @endif
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="button" style="color: #ffffff;">View Full Tracking Details</a>
            </div>

            <p style="background: #e8f5f5; padding: 15px; border-radius: 5px; border-left: 4px solid rgb(0, 127, 127);">
                <strong>💡 Tip:</strong> Save your tracking number <strong>{{ $trackingNumber }}</strong> for future reference. You can track your shipment anytime at <a href="{{ $trackingUrl }}" style="color: rgb(0, 127, 127);">forusfl.co.zm/track</a>
            </p>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
                Questions? Contact our support team at <strong>+260572788685</strong> (WhatsApp)<br>
                or visit <a href="https://forusfl.co.zm" style="color: rgb(0, 127, 127);">forusfl.co.zm</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Forus Freight Ltd. All rights reserved.</p>
            <p>Kafure Road, Lusaka, Zambia</p>
        </div>
    </div>
</body>
</html>
