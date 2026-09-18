@extends(Auth::check() ? 'layouts.dashboard' : 'layouts.guest-shipment')

@section('title', 'New Shipment Request - Forus Freight')

@section('styles')
<style>
    :root {
        --teal: #0f766e;
        --teal-600: #0d9488;
        --teal-soft: #f0fdfa;
        --orange: #ff6200;
        --ink: #0f172a;
        --slate: #64748b;
        --line: #e2e8f0;
        --panel: #ffffff;
        --radius: 20px;
    }

    * { box-sizing: border-box; }

    .form-container { max-width: 1020px; margin: 0 auto; }

    /* ── Page hero ── */
    .page-hero {
        background: linear-gradient(135deg, #0f766e 0%, #134e4a 60%, #0f172a 100%);
        border-radius: 24px;
        padding: 2rem 2.25rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.75rem;
        box-shadow: 0 16px 40px rgba(15, 118, 110, 0.22);
    }

    .page-hero h1 { font-size: 1.6rem; font-weight: 900; letter-spacing: -0.4px; margin: 0 0 0.3rem; }
    .page-hero p { margin: 0; opacity: 0.82; font-weight: 500; font-size: 0.9rem; }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        color: #0f766e;
        border: 1px solid #ffffff;
        padding: 0.55rem 1rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }

    .hero-chip:hover {
        background: #f0fdfa;
        border-color: #0f766e;
    }

    /* ── Action card ── */
    .form-card {
        background: var(--panel);
        border-radius: 28px;
        padding: 2rem 2.5rem;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.05);
        border: 1px solid var(--line);
        margin-top: 0.75rem;
    }

    .form-section { margin-bottom: 0.25rem; }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        margin-bottom: 1.75rem;
    }

    .form-section-header .icon-tile {
        width: 44px;
        height: 44px;
        background: var(--teal-soft);
        color: var(--teal);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    .form-section-header h3 { font-size: 1.12rem; font-weight: 800; color: var(--ink); margin: 0; }
    .form-section-header span { font-size: 0.8rem; color: var(--slate); font-weight: 600; }

    .input-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }

    .form-group { display: flex; flex-direction: column; gap: 0.5rem; }

    .form-group label {
        font-size: 0.72rem; font-weight: 800; color: var(--slate);
        text-transform: uppercase; letter-spacing: 0.03em;
        display: flex; align-items: center; gap: 0.4rem;
    }

    .form-group label .req { color: var(--orange); }

    .form-control {
        width: 100%;
        padding: 0.85rem 1.1rem;
        border: 1.5px solid var(--line);
        border-radius: 14px;
        font-size: 0.93rem;
        font-weight: 600;
        color: var(--ink);
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }

    .form-control::placeholder { color: #94a3b8; font-weight: 500; }

    .form-control:focus {
        outline: none;
        border-color: var(--teal-600);
        background: white;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
    }

    select.form-control { cursor: pointer; }

    /* ── Info note ── */
    .note {
        display: flex;
        gap: 0.7rem;
        background: var(--teal-soft);
        border: 1px solid rgba(13, 148, 136, 0.25);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        font-size: 0.8rem;
        color: #155e5a;
        font-weight: 600;
        line-height: 1.5;
        margin-top: 0.5rem;
    }
    .note i { color: var(--teal); margin-top: 0.15rem; }

    /* ── File drop zones ── */
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.25s;
    }

    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--teal-600);
        background: var(--teal-soft);
    }

    .upload-zone i { font-size: 2.4rem; color: #94a3b8; margin-bottom: 0.75rem; }
    .upload-zone h4 { font-weight: 800; color: var(--ink); margin: 0 0 0.3rem; font-size: 0.95rem; }
    .upload-zone p { font-size: 0.8rem; color: var(--slate); margin: 0; }

    /* ── Wizard ── */
    .wizard { margin-bottom: 2rem; }

    .wizard-progress { display: flex; align-items: center; gap: 0.3rem; margin-bottom: 1.1rem; }
    .wizard-step-dot { flex: 1; height: 5px; border-radius: 999px; background: #e2e8f0; transition: background 0.4s; }
    .wizard-step-dot.done { background: var(--teal-600); }

    .wizard-steps {
        display: flex;
        justify-content: space-between;
        gap: 0.4rem;
        overflow-x: auto;
        padding: 0.25rem 0;
    }

    .wizard-step-item {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
        position: relative;
    }

    .wizard-step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 21px;
        left: calc(50% + 24px);
        right: calc(-50% + 24px);
        height: 2px;
        background: #e2e8f0;
    }

    .wizard-step-item.done:not(:last-child)::after { background: var(--teal-600); }

    .wizard-step-badge {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        color: #94a3b8;
        background: #f1f5f9;
        border: 2px solid var(--line);
        transition: all 0.3s;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .wizard-step-item.done .wizard-step-badge {
        background: var(--teal-600);
        border-color: var(--teal-600);
        color: white;
    }

    .wizard-step-item.active .wizard-step-badge {
        background: var(--orange);
        border-color: var(--orange);
        color: white;
        box-shadow: 0 8px 18px rgba(255, 98, 0, 0.3);
    }

    .wizard-step-label { font-size: 0.68rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
    .wizard-step-item.active .wizard-step-label { color: var(--orange); }
    .wizard-step-item.done .wizard-step-label { color: var(--teal); }

    .wizard-step { display: none; }
    .wizard-step.active { display: block; animation: fadeStep 0.35s ease; }
    @keyframes fadeStep { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    .wizard-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 2.25rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--line);
    }

    .btn {
        border: none;
        border-radius: 14px;
        padding: 0.9rem 1.8rem;
        font-weight: 800;
        font-size: 0.92rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--teal) 0%, #115e59 100%);
        color: white;
        box-shadow: 0 10px 22px rgba(15, 118, 110, 0.28);
    }
    .btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(15, 118, 110, 0.34); }

    .btn-secondary {
        background: white;
        color: var(--slate);
        border: 1.5px solid var(--line);
    }
    .btn-secondary:hover { border-color: var(--teal-600); color: var(--teal); }

    .btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .wizard-counter { font-size: 0.78rem; font-weight: 700; color: #94a3b8; }

    /* ── Alerts ── */
    .alert {
        border-radius: 16px;
        padding: 1rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        gap: 0.7rem;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-error ul { margin: 0; padding-left: 1.2rem; }
    .alert-error li + li { margin-top: 0.35rem; }

    /* ── Animated walkthrough tutorial ── */
    .tutorial {
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
        border: 1.5px solid #fde6d2;
        border-radius: 22px;
        padding: 1.75rem 1.75rem 1.25rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(255, 98, 0, 0.06);
    }

    .tutorial-track { position: relative; min-height: 235px; }

    .tutorial-slide {
        position: absolute;
        inset: 0;
        display: flex;
        gap: 1.25rem;
        opacity: 0;
        transform: translateX(28px);
        pointer-events: none;
        transition: opacity 0.45s ease, transform 0.45s ease;
    }

    .tutorial-slide.active {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }

    .tutorial-slide.out-left { transform: translateX(-28px); }

    .tutorial-ghost {
        position: absolute;
        right: 0.25rem;
        top: -0.9rem;
        font-size: 4.5rem;
        font-weight: 900;
        line-height: 1;
        color: #ff6200;
        opacity: 0.10;
        letter-spacing: -2px;
        user-select: none;
        pointer-events: none;
    }

    .tutorial-step-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--orange);
        color: white;
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
        font-size: 0.9rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.7rem;
    }

    .tutorial-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 16px;
        background: white;
        border: 1px solid #fde6d2;
        box-shadow: 0 6px 16px rgba(255, 98, 0, 0.12);
        color: var(--orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .tutorial-slide h4 {
        margin: 0 0 0.55rem;
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--ink);
    }

    .tutorial-slide p { margin: 0 0 0.7rem; font-size: 1.15rem; color: var(--slate); line-height: 1.6; }

    .tutorial-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.1rem;
    }

    .tutorial-dots { display: flex; align-items: center; gap: 0.4rem; }

    .tutorial-dot {
        width: 8px;
        height: 8px;
        border: none;
        border-radius: 999px;
        background: #fcd9b8;
        cursor: pointer;
        padding: 0;
        transition: all 0.3s;
    }

    .tutorial-dot.active { width: 24px; background: var(--orange); }

    .tutorial-arrow {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid #fde6d2;
        background: white;
        color: #b45309;
        cursor: pointer;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        padding: 0;
    }

    .tutorial-arrow:hover { background: var(--orange); color: white; border-color: var(--orange); }

    .tutorial-progress {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 4px;
        background: #ffe9d6;
    }

    .tutorial-progress span {
        display: block;
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #ff6200, #ff9a4d);
    }

    .tutorial-pulse {
        display: inline-flex;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--orange);
        animation: pulse 1.6s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 98, 0, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(255, 98, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 98, 0, 0); }
    }

    .parcel-code {
        background: #0f172a;
        border-radius: 12px;
        padding: 0.8rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .parcel-code .code { color: #ffd166; font-weight: 800; font-size: 1.05rem; letter-spacing: 0.05em; }
    .parcel-code .copy {
        background: var(--teal); color: white; border: none; border-radius: 9px;
        padding: 0.4rem 0.75rem; font-size: 0.72rem; font-weight: 700; cursor: pointer;
    }
    .parcel-code .copy:hover { background: var(--teal-600); }

    .address-block {
        margin-top: 0.5rem;
        background: #f8fafc;
        border-radius: 10px;
        padding: 0.7rem 0.8rem;
        font-size: 1rem;
        color: #475569;
        line-height: 1.6;
    }

    /* ── Other-category reveal ── */
    .other-wrap { display: none; }
    .other-wrap.show { display: flex; animation: fadeStep 0.3s ease; }

    @media (max-width: 640px) {
        .input-grid { grid-template-columns: 1fr; }
        .form-card { padding: 1.5rem 1.25rem; }
        .page-hero { padding: 1.5rem; }
        .wizard-steps { gap: 0.75rem; }
        .wizard-step-badge { width: 38px; height: 38px; font-size: 0.85rem; }
        .wizard-step-item:not(:last-child)::after { top: 19px; }
    }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div>
        <h1>Request a New Shipment</h1>
        <p>Six quick steps — our team handles the logistics details after you submit.</p>
    </div>
    <a href="{{ route('client.shipments') }}" class="hero-chip">
        <i class="fas fa-arrow-left"></i> My Shipments
    </a>
</div>

<div class="form-container">
    @guest
    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-circle-exclamation"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @endguest

    @php
        // Step containing the first failed field, so the wizard reopens where
        // the problem is instead of dumping the user back at step one.
        $fieldSteps = [
            'client_name' => 1, 'client_phone' => 1,
            'origin_country' => 2, 'origin_city' => 2, 'port_of_origin' => 2,
            'destination_country' => 2, 'destination_city' => 2, 'port_destination' => 2,
            'tracking_number' => 3, 'service_type' => 3, 'shipping_method' => 3,
            'description' => 4, 'no_of_parcels' => 4,
            'images' => 6,
        ];
        $errorStep = (!isset($errors) || $errors->isEmpty())
            ? 1
            : (collect($errors->keys())->first(fn ($k) => isset($fieldSteps[$k])) ?: 1);
    @endphp

    <!-- Animated walkthrough: how a shipment works -->
    <div class="tutorial" id="tutorial">
        <div class="tutorial-track" id="tutorialTrack">
            <div class="tutorial-slide active" data-slide="0">
                <span class="tutorial-ghost">01</span>
                <div class="tutorial-icon"><i class="fas fa-cart-shopping"></i></div>
                <div>
                    <span class="tutorial-step-chip"><span class="tutorial-pulse"></span> Step 1 / 3</span>
                    <h4>Order online</h4>
                    <p>Buy from Alibaba, eBay, Amazon, Shein or any store and ship to our China warehouse.</p>
                    <div class="address-block">
                        <strong>{{ config('forus.forwarding_address.name') }}</strong><br>
                        {{ config('forus.forwarding_address.line1') }}, {{ config('forus.forwarding_address.line2') }}<br>
                        {{ config('forus.forwarding_address.line3') }}, {{ config('forus.forwarding_address.country') }}<br>
                        {{ config('forus.forwarding_address.address_cn') }}<br>
                        📞 {{ config('forus.forwarding_address.phone') }} · ✉️ {{ config('forus.forwarding_address.email') }}
                    </div>
                </div>
            </div>

            <div class="tutorial-slide" data-slide="1">
                <span class="tutorial-ghost">02</span>
                <div class="tutorial-icon"><i class="fas fa-tag"></i></div>
                <div>
                    <span class="tutorial-step-chip"><span class="tutorial-pulse"></span> Step 2 / 3</span>
                    <h4>Label every parcel</h4>
                    <p>Add our Shipping Mark plus your name &amp; phone (the mark routes parcels to <strong>Zambia</strong>).</p>
                    <div class="parcel-code">
                        <span class="code" id="parcelCodeDisplay">{{ $parcelCode }}</span>
                        <button type="button" class="copy" onclick="copyParcelCode()"><i class="far fa-copy"></i> Copy</button>
                    </div>
                    <p style="font-size:1rem;color:#94a3b8;margin:0.7rem 0 0;">Routed to Zambia. A <strong>GHFFL</strong> mark would route to Ghana instead.</p>
                </div>
            </div>

            <div class="tutorial-slide" data-slide="2">
                <span class="tutorial-ghost">03</span>
                <div class="tutorial-icon"><i class="fas fa-truck-fast"></i></div>
                <div>
                    <span class="tutorial-step-chip"><span class="tutorial-pulse"></span> Step 3 / 3</span>
                    <h4>We receive &amp; ship</h4>
                    <p>Complete this form and we'll receive your goods, assign your Serial Number, and confirm dates &amp; pricing after review.</p>
                    <a href="{{ route('client.getting-started') }}" style="color:#007f7f;font-size:0.82rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;">
                        See full walkthrough <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="tutorial-nav">
            <button type="button" class="tutorial-arrow" id="tutorialPrev" aria-label="Previous step"><i class="fas fa-chevron-left"></i></button>
            <div class="tutorial-dots" id="tutorialDots"></div>
            <button type="button" class="tutorial-arrow" id="tutorialNext" aria-label="Next step"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="tutorial-progress"><span id="tutorialBar"></span></div>
    </div>

    <div class="form-card">
        <form action="{{ route('client.shipments.store') }}" method="POST" enctype="multipart/form-data" id="shipmentForm" data-loading-label="Submitting shipment…">
            @csrf

            <!-- Wizard stepper -->
            <div class="wizard">
                <div class="wizard-progress" id="wizardProgress"></div>
                <div class="wizard-steps" id="wizardSteps">
                    <div class="wizard-step-item" data-wiz-step="1"><div class="wizard-step-badge"><i class="fas fa-user"></i></div><div class="wizard-step-label">Details</div></div>
                    <div class="wizard-step-item" data-wiz-step="2"><div class="wizard-step-badge"><i class="fas fa-route"></i></div><div class="wizard-step-label">Route</div></div>
                    <div class="wizard-step-item" data-wiz-step="3"><div class="wizard-step-badge"><i class="fas fa-box"></i></div><div class="wizard-step-label">Shipment</div></div>
                    <div class="wizard-step-item" data-wiz-step="4"><div class="wizard-step-badge"><i class="fas fa-boxes-stacked"></i></div><div class="wizard-step-label">Cargo</div></div>
                    <div class="wizard-step-item" data-wiz-step="5"><div class="wizard-step-badge"><i class="fas fa-file-invoice"></i></div><div class="wizard-step-label">Docs</div></div>
                    <div class="wizard-step-item" data-wiz-step="6"><div class="wizard-step-badge"><i class="fas fa-camera"></i></div><div class="wizard-step-label">Photos</div></div>
                </div>
            </div>

            <!-- Step 1: Client Information -->
            <div class="wizard-step" data-step="1">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-user"></i></div>
                        <div><h3>Client Information</h3><span>Who is this shipment for?</span></div>
                    </div>
                    <div class="input-grid">
                        <div class="form-group">
                            <label for="field_client_name">Client Name <span class="req">*</span></label>
                            <input id="field_client_name" type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="e.g. ADRIAN CHUNGA" required>
                        </div>
                        <div class="form-group">
                            <label for="field_client_phone">Phone Number <span class="req">*</span></label>
                            <input id="field_client_phone" type="tel" name="client_phone" class="form-control" value="{{ old('client_phone') }}" placeholder="e.g. 260970026344" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Route & Port Details -->
            <div class="wizard-step" data-step="2">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-route"></i></div>
                        <div><h3>Route &amp; Ports</h3><span>Where is the cargo coming from and going to?</span></div>
                    </div>
                    <div class="input-grid">
                        <div class="form-group">
                            <label for="origin_country">Origin Country <span class="req">*</span></label>
                            <select id="origin_country" name="origin_country" class="form-control" onchange="updateCities('origin')" required>
                                <option value="" disabled {{ old('origin_country') ? '' : 'selected' }}>Select country...</option>
                                @foreach(['China','Zambia','South Africa','Zimbabwe','Botswana','Namibia','DRC','Tanzania','Malawi','Mozambique'] as $country)
                                    <option value="{{ $country }}" @selected(old('origin_country') === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="origin_city">Origin City <span class="req">*</span></label>
                            <select id="origin_city" name="origin_city" class="form-control" required data-old-value="{{ old('origin_city') }}">
                                <option value="" selected disabled>Select country first...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field_port_of_origin">Port of Origin</label>
                            <input id="field_port_of_origin" type="text" name="port_of_origin" class="form-control" value="{{ old('port_of_origin') }}" placeholder="e.g. GUANGZHOU PORT">
                        </div>
                        <div class="form-group">
                            <label for="destination_country">Destination Country <span class="req">*</span></label>
                            <select id="destination_country" name="destination_country" class="form-control" onchange="updateCities('destination')" required>
                                <option value="" disabled {{ old('destination_country') ? '' : 'selected' }}>Select country...</option>
                                @foreach(['Zambia','South Africa','Zimbabwe','Botswana','Namibia','DRC','Tanzania','Malawi','Mozambique'] as $country)
                                    <option value="{{ $country }}" @selected(old('destination_country') === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="destination_city">Destination City <span class="req">*</span></label>
                            <select id="destination_city" name="destination_city" class="form-control" required data-old-value="{{ old('destination_city') }}">
                                <option value="" selected disabled>Select country first...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field_port_destination">Port Destination</label>
                            <input id="field_port_destination" type="text" name="port_destination" class="form-control" value="{{ old('port_destination') }}" placeholder="e.g. PORT OF BEIRA (MOZAMBIQUE)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Shipment Details -->
            <div class="wizard-step" data-step="3">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-box"></i></div>
                        <div><h3>Shipment Details</h3><span>Your parcel code is already assigned.</span></div>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <div class="note">
                            <i class="fas fa-magic"></i>
                            <span>Parcel Code <strong>{{ $parcelCode }}</strong> — write it on every parcel so it is routed to <strong>Zambia (ZMFFL)</strong>. Serial Number is auto-generated.</span>
                        </div>
                        <input type="hidden" name="code" value="{{ old('code', $parcelCode) }}">
                    </div>
                    <div class="input-grid" style="margin-top:1.25rem;">
                        <div class="form-group">
                            <label for="field_tracking_number">Carrier Tracking Number</label>
                            <input id="field_tracking_number" type="text" name="tracking_number" class="form-control" value="{{ old('tracking_number') }}" placeholder="e.g. 610080707216">
                            <span style="font-size:0.72rem;color:#94a3b8;">Optional — from your courier if you already have one.</span>
                        </div>
                        <div class="form-group">
                            <label for="field_service_type">Service Type <span class="req">*</span></label>
                            <select id="field_service_type" name="service_type" class="form-control" required>
                                @foreach(['IMPORT','EXPORT','Road Freight','Air Freight','Sea Freight','Express Delivery'] as $svc)
                                    <option value="{{ $svc }}" @selected(old('service_type') === $svc)>{{ $svc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field_shipping_method">Shipping Method <span class="req">*</span></label>
                            <select id="field_shipping_method" name="shipping_method" class="form-control" required>
                                @foreach(['SEA','AIR','ROAD','RAIL'] as $method)
                                    <option value="{{ $method }}" @selected(old('shipping_method') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="note" style="margin-top:1.25rem;">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Our team confirms your <strong>Date of Load</strong> and estimated delivery (ETA) once your goods reach our warehouse.</span>
                    </div>
                </div>
            </div>

            <!-- Step 4: Cargo Details -->
            <div class="wizard-step" data-step="4">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-boxes-stacked"></i></div>
                        <div><h3>Cargo Details</h3><span>What are you sending?</span></div>
                    </div>
                    <div class="input-grid">
                        @php
                            $categories = ['Bathroom Supplies','Electronics & Appliances','Clothing & Textiles','Cosmetics & Beauty Products','Automotive & Spare Parts','Home & Furniture','Industrial & Machinery','Construction Materials','Groceries & Food Items','Medical & Pharmaceutical','Personal Effects & Luggage'];
                            $oldDescription = old('description');
                            $descIsCustom = $oldDescription && !in_array($oldDescription, $categories, true);
                        @endphp
                        <div class="form-group">
                            <label for="field_description">Description of Goods <span class="req">*</span></label>
                            <select id="field_description" name="description" class="form-control" onchange="toggleOther()" required>
                                <option value="" disabled @selected(!$oldDescription)>Select goods type...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @selected(!$descIsCustom && $oldDescription === $cat)>{{ $cat }}</option>
                                @endforeach
                                <option value="Other (specify below)" @selected($descIsCustom)>Other (specify below)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="field_no_of_parcels">No. of Parcels <span class="req">*</span></label>
                            <input id="field_no_of_parcels" type="number" name="no_of_parcels" class="form-control" value="{{ old('no_of_parcels') }}" placeholder="e.g. 3" min="1" required>
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <div class="other-wrap @if($descIsCustom) show @endif" id="otherWrap">
                                <label for="field_description_other">Please specify goods</label>
                                <input id="field_description_other" type="text" class="form-control" value="{{ $descIsCustom ? $oldDescription : '' }}" placeholder="e.g. Antiques, musical instruments..." maxlength="255">
                            </div>
                            <div class="note">
                                <i class="fas fa-weight-hanging"></i>
                                <span>Our warehouse measures exact weight &amp; volume (CBM) on arrival, and our team confirms your cost — you don't need to guess these.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Documents -->
            <div class="wizard-step" data-step="5">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-file-invoice"></i></div>
                        <div><h3>Documentation</h3><span>Optional but helpful.</span></div>
                    </div>
                    <div class="upload-zone" id="dropZone">
                        <i class="fas fa-cloud-arrow-up"></i>
                        <h4>Upload Shipping Docs</h4>
                        <p>Invoices, receipts, or customs forms — drag &amp; drop or click to browse.</p>
                        <input type="file" name="documents[]" id="fileInput" style="display:none;" multiple>
                    </div>
                </div>
            </div>

            <!-- Step 6: Shipment Images -->
            <div class="wizard-step" data-step="6">
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="icon-tile"><i class="fas fa-camera"></i></div>
                        <div><h3>Shipment Images</h3><span>Photos of cargo, packaging or labels.</span></div>
                    </div>
                    <div class="upload-zone" id="imageDropZone">
                        <i class="fas fa-images"></i>
                        <h4>Upload Cargo Images</h4>
                        <p>JPG, PNG or WebP · max 5 MB each</p>
                        <input type="file" name="images[]" id="imageInput" style="display:none;" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                    </div>
                    <div id="imagePreview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin-top:1.25rem;"></div>
                    <div id="uploadErrors"></div>
                </div>
            </div>

            <div class="wizard-nav">
                <button type="button" class="btn btn-secondary" id="btnBack" style="visibility:hidden;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <span class="wizard-counter" id="wizardCounter">Step 1 of 6</span>
                <button type="button" class="btn btn-primary" id="btnNext">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const cityData = {
        'China': ['Guangzhou', 'Shenzhen', 'Shanghai', 'Ningbo', 'Qingdao', 'Xiamen', 'Yiwu'],
        'Zambia': ['Lusaka', 'Kitwe', 'Ndola', 'Livingstone', 'Chipata', 'Kabwe', 'Solwezi'],
        'South Africa': ['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth', 'East London'],
        'Zimbabwe': ['Harare', 'Bulawayo', 'Mutare', 'Gweru', 'Victoria Falls'],
        'Botswana': ['Gaborone', 'Francistown', 'Maun', 'Lobatse'],
        'Namibia': ['Windhoek', 'Walvis Bay', 'Swakopmund', 'Luderitz'],
        'DRC': ['Lubumbashi', 'Kinshasa', 'Goma', 'Kolwezi'],
        'Tanzania': ['Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma'],
        'Malawi': ['Lilongwe', 'Blantyre', 'Mzuzu', 'Zomba'],
        'Mozambique': ['Maputo', 'Beira', 'Nampula', 'Tete']
    };

    function updateCities(type) {
        const countrySelect = document.getElementById(type + '_country');
        const citySelect = document.getElementById(type + '_city');
        const selectedCountry = countrySelect.value;

        citySelect.innerHTML = '<option value="" disabled selected>Select City...</option>';
        citySelect.disabled = false;

        if (cityData[selectedCountry]) {
            cityData[selectedCountry].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
            const otherOption = document.createElement('option');
            otherOption.value = 'Other';
            otherOption.textContent = 'Other';
            citySelect.appendChild(otherOption);
        }
    }

    /* Repopulate city selects after a validation redirect. */
    function repopulateCities(type) {
        const country = document.getElementById(type + '_country');
        const oldCity = document.getElementById(type + '_city').dataset.oldValue;
        if (country.value && cityData[country.value]) {
            updateCities(type);
            if (oldCity) {
                const citySelect = document.getElementById(type + '_city');
                const match = Array.from(citySelect.options).find(o => o.value === oldCity);
                if (match) citySelect.value = oldCity;
            }
        }
    }

    /* "Other (specify below)" reveal + sync. */
    const OTHER_VALUE = 'Other (specify below)';

    function toggleOther() {
        const sel = document.getElementById('field_description');
        const wrap = document.getElementById('otherWrap');
        wrap.classList.toggle('show', sel.value === OTHER_VALUE);
    }

    /* ---------- Multi-step wizard ---------- */
    const TOTAL_STEPS = 6;
    const form = document.getElementById('shipmentForm');
    const stepPanels = Array.from(form.querySelectorAll('.wizard-step'));
    const stepItems = Array.from(document.querySelectorAll('.wizard-step-item'));
    const progBar = document.getElementById('wizardProgress');
    const btnBack = document.getElementById('btnBack');
    const btnNext = document.getElementById('btnNext');
    const wizardCounter = document.getElementById('wizardCounter');

    let currentStep = {{ $errorStep }};

    function buildProgressBar() {
        progBar.innerHTML = '';
        for (let i = 1; i <= TOTAL_STEPS; i++) {
            const dot = document.createElement('div');
            dot.className = 'wizard-step-dot' + (i < currentStep ? ' done' : '');
            progBar.appendChild(dot);
        }
    }

    function renderSteps() {
        stepPanels.forEach(panel => {
            panel.classList.toggle('active', Number(panel.dataset.step) === currentStep);
        });
        stepItems.forEach(item => {
            const n = Number(item.dataset.wizStep);
            item.classList.toggle('active', n === currentStep);
            item.classList.toggle('done', n < currentStep);
        });
        buildProgressBar();
        btnBack.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
        btnNext.innerHTML = currentStep === TOTAL_STEPS
            ? 'Send Request <i class="fas fa-paper-plane"></i>'
            : 'Next <i class="fas fa-arrow-right"></i>';
        wizardCounter.textContent = `Step ${currentStep} of ${TOTAL_STEPS}`;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep() {
        const panel = stepPanels.find(p => Number(p.dataset.step) === currentStep);
        const fields = panel.querySelectorAll('input, select, textarea');
        for (const f of fields) {
            if (f.disabled) continue;
            if (!f.checkValidity()) {
                f.reportValidity();
                return false;
            }
        }
        return true;
    }

    function nextStep() {
        if (currentStep === TOTAL_STEPS) {
            // Resolve a custom "Other" goods description before submitting.
            const sel = document.getElementById('field_description');
            if (sel.value === OTHER_VALUE) {
                const other = document.getElementById('field_description_other').value.trim();
                sel.value = other || OTHER_VALUE;
            }
            form.requestSubmit();
            return;
        }
        if (!validateStep()) return;
        currentStep++;
        renderSteps();
    }

    function prevStep() {
        if (currentStep === 1) return;
        currentStep--;
        renderSteps();
    }

    btnNext.addEventListener('click', nextStep);
    btnBack.addEventListener('click', prevStep);

    form.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            nextStep();
        }
    });

form.addEventListener('submit', () => {
        btnNext.disabled = true;
        btnNext.innerHTML = (form.dataset.loadingLabel || 'Sending...') + ' <i class="fas fa-spinner fa-spin"></i>';
    });

    renderSteps();
    toggleOther();
    repopulateCities('origin');
    repopulateCities('destination');

    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            dropZone.style.borderColor = '#0d9488';
            dropZone.style.background = '#f0fdfa';
            dropZone.querySelector('p').textContent = fileInput.files.length + ' file(s) selected';
        }
    });

    const imageDropZone = document.getElementById('imageDropZone');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    /* Client-side only — a fast, friendly first check so people don't wait
       for a round-trip to find out a file is the wrong type or too big.
       This is NOT the real security boundary: the server always re-checks
       and re-encodes every image regardless of what the browser reports
       here, since a script can trivially send whatever it wants. */
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    const MAX_IMAGE_BYTES = 5 * 1024 * 1024;
    let stagedImages = new DataTransfer();

    function showUploadErrors(messages) {
        const box = document.getElementById('uploadErrors');
        if (!messages.length) { box.innerHTML = ''; return; }
        box.innerHTML = messages
            .map(m => `<div style="color:#ef4444;font-size:0.8rem;margin-top:0.5rem;">${m}</div>`)
            .join('');
    }

    function renderImagePreviews() {
        imagePreview.innerHTML = '';
        Array.from(stagedImages.files).forEach((file, i) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:14px;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,0.12);';
            div.innerHTML = `
                <img src="${url}" style="width:100%;height:150px;object-fit:cover;display:block;">
                <button type="button" onclick="removeStagedImage(${i})" title="Remove"
                        style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(0,0,0,0.65);color:#fff;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
                <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:white;padding:0.5rem;font-size:0.72rem;font-weight:600;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                    ${file.name}
                </div>
            `;
            imagePreview.appendChild(div);
        });
    }

    window.removeStagedImage = function (index) {
        const newDt = new DataTransfer();
        Array.from(stagedImages.files).forEach((f, i) => { if (i !== index) newDt.items.add(f); });
        stagedImages = newDt;
        imageInput.files = stagedImages.files;
        renderImagePreviews();
    };

    imageDropZone.addEventListener('click', () => imageInput.click());

    imageDropZone.addEventListener('dragover', (e) => { e.preventDefault(); imageDropZone.classList.add('dragover'); });
    imageDropZone.addEventListener('dragleave', () => imageDropZone.classList.remove('dragover'));
    imageDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        imageDropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });

    imageInput.addEventListener('change', () => {
        const errors = [];
        Array.from(imageInput.files).forEach((file) => {
            if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                errors.push(`"${file.name}" isn't a supported image type (JPG, PNG, or WebP only).`);
                return;
            }
            if (file.size > MAX_IMAGE_BYTES) {
                errors.push(`"${file.name}" is ${(file.size / (1024 * 1024)).toFixed(1)} MB — the limit is 5 MB.`);
                return;
            }
            stagedImages.items.add(file);
        });
        showUploadErrors(errors);
        imageInput.files = stagedImages.files;
        if (stagedImages.files.length > 0) {
            imageDropZone.style.borderColor = '#0d9488';
            imageDropZone.style.background = '#f0fdfa';
        }
        renderImagePreviews();
    });

    function copyParcelCode() {
        const el = document.getElementById('parcelCodeDisplay');
        if (!el) return;
        const text = el.textContent.trim();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => flashCopy());
        } else {
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            flashCopy();
        }
    }

function flashCopy() {
        const btn = document.querySelector('.parcel-code .copy');
        if (!btn) return;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied';
        setTimeout(() => { btn.innerHTML = '<i class="far fa-copy"></i> Copy'; }, 2000);
    }

    (function tutorialPlayer() {
        const track = document.getElementById('tutorialTrack');
        const slides = Array.from(track.children);
        const dotsWrap = document.getElementById('tutorialDots');
        const bar = document.getElementById('tutorialBar');
        const prevBtn = document.getElementById('tutorialPrev');
        const nextBtn = document.getElementById('tutorialNext');
        if (!track || slides.length < 2) return;

        const DURATION = 8000;
        let idx = 0;
        let timer = null;

        const dots = slides.map((_, i) => {
            const d = document.createElement('button');
            d.type = 'button';
            d.className = 'tutorial-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', 'Step ' + (i + 1));
            d.addEventListener('click', () => go(i));
            dotsWrap.appendChild(d);
            return d;
        });

        function applyIndex() {
            slides.forEach((s, i) => s.classList.toggle('active', i === idx));
            dots.forEach((d, i) => d.classList.toggle('active', i === idx));
            bar.style.transition = 'none';
            bar.style.width = '0%';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    bar.style.transition = 'width ' + DURATION + 'ms linear';
                    bar.style.width = '100%';
                });
            });
        }

        function go(i) {
            idx = (i + slides.length) % slides.length;
            applyIndex();
            resetTimer();
        }

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(() => go(idx + 1), DURATION);
        }

        prevBtn.addEventListener('click', () => go(idx - 1));
        nextBtn.addEventListener('click', () => go(idx + 1));

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) clearInterval(timer);
            else resetTimer();
        });

        applyIndex();
        resetTimer();
    })();
</script>
@endsection