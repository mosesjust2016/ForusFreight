<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Release Instruction · {{ $trackingNumber }}</title>
    <style>
        @page { size: A4 portrait; margin: 9mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #0d1b20; font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; line-height: 1.35; }
        table { width: 100%; border-collapse: collapse; }
        .ticket { border: 1px solid #cbd5da; border-radius: 12px; overflow: hidden; }
        .header { padding: 14px 16px 12px; border-bottom: 4px solid #ff6200; }
        .logo { width: 150px; max-height: 62px; object-fit: contain; object-position: left center; }
        .brand { font-size: 16pt; font-weight: 800; margin: 4px 0 2px; }
        .muted { color: #5d7076; }
        .small { font-size: 7.5pt; }
        .right { text-align: right; }
        .title { display: inline-block; background: #ff6200; color: #fff; padding: 5px 9px; border-radius: 4px; font-size: 10pt; font-weight: 800; letter-spacing: .8px; }
        .doc-no { margin: 5px 0 0; font-family: DejaVu Sans Mono, monospace; font-size: 8pt; }
        .tracking { background: #f4f7f7; border-bottom: 1px dashed #aebabe; padding: 9px 16px; }
        .tracking-label { color: #5d7076; font-size: 7.5pt; text-transform: uppercase; letter-spacing: .7px; }
        .tracking-number { font-family: DejaVu Sans Mono, monospace; font-size: 13pt; font-weight: 800; letter-spacing: 1px; }
        .tracking-barcode { width: 175px; height: 30px; }
        .body { padding: 13px 16px 10px; }
        .section { margin: 0 0 11px; }
        .section-title { color: #005e5e; border-bottom: 2px solid #ff6200; padding-bottom: 3px; margin: 0 0 6px; font-size: 8pt; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; }
        .cell { border: 1px solid #d9e0e2; padding: 7px 8px; vertical-align: top; }
        .label { display: block; color: #65777c; font-size: 6.8pt; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
        .value { font-weight: 700; }
        .value.mono { font-family: DejaVu Sans Mono, monospace; font-weight: 500; }
        .cargo th { background: #f0f3f4; color: #3b4d52; text-align: left; font-size: 7pt; text-transform: uppercase; padding: 6px 7px; }
        .cargo td { border: 1px solid #d9e0e2; padding: 7px; vertical-align: top; }
        .num { text-align: right; }
        .verify { width: 22%; text-align: center; vertical-align: top; }
        .verify img { width: 78px; height: 78px; }
        .verify small { display: block; color: #65777c; font-size: 6.5pt; }
        .barcode { width: 100%; height: 42px; }
        .barcode img { width: 100%; height: 42px; }
        .barcode-text { font-family: DejaVu Sans Mono, monospace; text-align: center; font-size: 7pt; letter-spacing: 1px; }
        .sign td { width: 50%; height: 70px; border: 1px solid #d9e0e2; padding: 7px; vertical-align: top; }
        .sign-line { border-bottom: 1px solid #0d1b20; margin-top: 30px; }
        .footer { border-top: 1px solid #d9e0e2; padding-top: 7px; color: #65777c; font-size: 6.8pt; }
    </style>
</head>
<body>
<div class="ticket">
    <table class="header"><tr>
        <td style="vertical-align:top;">
            @if($logoData)<img class="logo" src="{{ $logoData }}" alt="Forus Freight">@endif
            <div class="brand">{{ $company['name'] }}</div>
            <div class="muted small">{{ config('forus.warehouse_address.line1') }} · {{ config('forus.warehouse_address.line2') }}</div>
            <div class="muted small">{{ config('forus.warehouse_address.city') }}</div>
            <div class="muted small">{{ $company['phone'] }} · {{ $company['email'] }}</div>
        </td>
        <td class="right" style="vertical-align:top;"><div class="title">RELEASE INSTRUCTION</div><p class="doc-no"><strong>{{ $noteNumber }}</strong></p><p class="muted small">Issued {{ $issuedAt->format('d M Y') }}</p></td>
    </tr></table>

    <table class="tracking"><tr>
        <td style="vertical-align:middle;"><span class="tracking-label">Tracking number</span><br><span class="tracking-number">{{ $trackingNumber }}</span></td>
        <td class="right" style="vertical-align:middle;">@if($barcodeTopSvg)<img class="tracking-barcode" src="{{ $barcodeTopSvg }}" alt="{{ $trackingNumber }}"><br>@endif<span class="small muted">{{ $trackingNumberClean }}</span></td>
    </tr></table>

    <div class="body">
        <div class="section"><div class="section-title">Shipment details</div><table>
            <tr><td class="cell"><span class="label">Shipment serial</span><span class="value mono">{{ $shipment->serial_no ?: '—' }}</span></td><td class="cell"><span class="label">Client</span><span class="value">{{ $shipment->client_name ?: ($shipment->user->name ?? '—') }}</span></td><td class="cell"><span class="label">Client phone</span><span class="value">{{ $shipment->client_phone ?: ($shipment->phone_number ?: ($shipment->user->phone ?? '—')) }}</span></td></tr>
            <tr><td class="cell"><span class="label">Origin</span><span class="value">{{ $shipment->origin ?: '—' }}</span></td><td class="cell"><span class="label">Destination</span><span class="value">{{ $shipment->destination ?: '—' }}</span></td><td class="cell"><span class="label">Status</span><span class="value">{{ $shipment->status_label ?: '—' }}</span></td></tr>
            <tr><td class="cell"><span class="label">Service</span><span class="value">{{ $shipment->service ?: ($shipment->service_type ?: '—') }}</span></td><td class="cell"><span class="label">Shipping method</span><span class="value">{{ $shipment->shipping_method ?: '—' }}</span></td><td class="cell"><span class="label">Reference</span><span class="value">{{ $shipment->reference ?: '—' }}</span></td></tr>
        </table></div>

        <div class="section"><div class="section-title">Cargo</div><table class="cargo"><tr><th>Description</th><th>Parcel code</th><th class="num">Parcels</th><th class="num">CBM</th><th class="num">Gross weight</th></tr><tr>
            <td>{{ $shipment->description ?: 'General cargo' }}</td><td>{{ $shipment->code ?: '—' }}</td><td class="num">{{ $shipment->no_of_parcels ?: ($shipment->quantity ?: '—') }}</td><td class="num">{{ $shipment->cbm_volume !== null ? $shipment->cbm_volume : '—' }}</td><td class="num">{{ $shipment->gross_weight !== null ? $shipment->gross_weight.' kg' : ($shipment->weight !== null ? $shipment->weight.' kg' : '—') }}</td>
        </tr></table></div>

        <div class="section"><div class="section-title">Movement and delivery</div><table>
            <tr><td class="cell"><span class="label">Date loaded</span><span class="value">{{ $shipment->date_of_load?->format('d M Y') ?: '—' }}</span></td><td class="cell"><span class="label">Estimated delivery</span><span class="value">{{ $shipment->estimated_delivery?->format('d M Y') ?: '—' }}</span></td><td class="cell"><span class="label">Delivery date</span><span class="value">{{ $shipment->delivery_date?->format('d M Y') ?: '—' }}</span></td></tr>
            <tr><td class="cell"><span class="label">Current border / location</span><span class="value">{{ $shipment->current_border ?: '—' }}</span></td><td class="cell"><span class="label">Driver</span><span class="value">{{ $shipment->driver ?: '—' }}</span></td><td class="cell"><span class="label">Vehicle registration</span><span class="value">{{ $shipment->vehicle_registration ?: '—' }}</span></td></tr>
        </table></div>

        <div class="section"><div class="section-title">Forus Freight warehouse</div><p class="small" style="margin:0;">{{ config('forus.warehouse_address.name') }} · {{ config('forus.warehouse_address.line1') }}, {{ config('forus.warehouse_address.line2') }}, {{ config('forus.warehouse_address.city') }}</p></div>

        <table style="margin: 4px 0 11px;"><tr><td style="vertical-align:top; padding-right:14px;">@if($barcodeBottomSvg)<div class="barcode"><img src="{{ $barcodeBottomSvg }}" alt="{{ $trackingNumberClean }}"></div>@endif<div class="barcode-text">{{ $trackingNumberClean }}</div></td><td class="verify"><img src="{{ $verificationQr }}" alt="Verify release instruction"><small>Verify document<br>{{ $noteNumber }}</small></td></tr></table>

        <div class="section"><div class="section-title">Delivery confirmation</div><p class="small" style="margin:0 0 7px;">The shipment details above confirm the recorded handover at {{ $shipment->destination ?: 'the destination' }}.</p><table class="sign"><tr><td>Delivered by<div class="sign-line"></div><span class="small muted">Name and signature</span></td><td>Received by<div class="sign-line"></div><span class="small muted">Name and signature</span></td></tr></table></div>
        <div class="footer">{{ $company['name'] }} · {{ $company['registration'] }} · Document reference {{ $noteNumber }}</div>
    </div>
</div>
</body>
</html>
