@extends('layouts.dashboard')

@section('title', 'Getting Started - Forus Freight')

@section('styles')
<style>
    .walkthrough-wrap {
        max-width: 900px;
        margin: 0 auto;
    }

    .walkthrough-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }

    .step-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #f0f9f9;
        color: #007f7f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .step-tag {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .step-num {
        font-size: 0.75rem;
        font-weight: 800;
        color: #007f7f;
        background: #e6f7f5;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }

    .step-tag h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .step-tag p {
        font-size: 0.8rem;
        color: #94a3b8;
        margin: 0;
    }

    .step-body {
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.65;
    }

    .step-body ul {
        margin: 0.75rem 0 0;
        padding-left: 1.25rem;
    }

    .step-body li {
        margin-bottom: 0.5rem;
    }

    .code-chip {
        display: inline-block;
        background: #1e293b;
        color: #ffd166;
        font-family: ui-monospace, 'SF Mono', monospace;
        font-weight: 800;
        padding: 0.35rem 0.9rem;
        border-radius: 10px;
        font-size: 0.95rem;
        letter-spacing: 0.04em;
        margin: 0.25rem 0;
    }

    .address-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-left: 4px solid #007f7f;
        border-radius: 14px;
        padding: 1.25rem;
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.6;
        margin-top: 0.75rem;
    }

    .announcement-box {
        background: #1e293b;
        border: 2px solid #ff6200;
        border-radius: 20px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: white;
    }

    .danger-note {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        margin-top: 1rem;
    }

    .success-note {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 4px solid #22c55e;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        margin-top: 1rem;
    }

    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .benefit-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        background: #f8fafc;
        border-radius: 14px;
        padding: 1.1rem 1.15rem;
    }

    .benefit-item i {
        color: #22c55e;
        margin-top: 0.15rem;
    }

    .benefit-item p {
        font-size: 0.83rem;
        color: #1e293b;
        font-weight: 700;
        margin: 0;
        line-height: 1.4;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .quick-link {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1.25rem;
        border-radius: 16px;
        background: white;
        border: 1px solid #f1f5f9;
        text-decoration: none;
        transition: all 0.3s;
    }

    .quick-link:hover {
        border-color: #007f7f;
        box-shadow: 0 8px 24px rgba(0,127,127,0.1);
        transform: translateY(-2px);
    }

    .quick-link i {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .quick-link h4 {
        font-size: 0.9rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.15rem;
    }

    .quick-link p {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 0;
    }

    @media (max-width: 640px) {
        .walkthrough-card { padding: 1.5rem; }
    }
</style>
@section('content')
<div class="welcome-section">
    <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Ship from China to Zambia</h1>
    <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">With Forus Freight, your China-to-Zambia shipping journey is designed to be simple — from the moment you place your order until your goods arrive. Here is how it works, step by step.</p>
</div>

<div class="walkthrough-wrap">

    <!-- SPECIAL ANNOUNCEMENT -->
    <div class="announcement-box">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.9rem;">
            <i class="fas fa-bullhorn" style="color: #ffd166; font-size: 1.4rem;"></i>
            <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0;">Important — For all Forus Freight Customers</h2>
        </div>
        <p style="margin: 0; font-size: 0.9rem; opacity: 0.95; line-height: 1.6;">
            Always add our <strong style="color: #ffd166;">Shipping Mark: ZMFFL</strong> when you deliver your consignments to our China warehouse, and clearly capture the <strong>customer name</strong> and <strong>contact details</strong> on the parcel. The mark routes your parcel to <strong style="color: #ffd166;">ZAMBIA</strong> — without it your goods cannot be allocated to your account.
        </p>
    </div>

    <!-- Step 01: Create your account -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-user-plus"></i></div>
            <div>
                <span class="step-num">01</span>
                <h3>Create Your Account</h3>
                <p>Register with Forus Freight</p>
            </div>
        </div>
        <div class="step-body">
            <p>Sign up for your Forus Freight account to receive your unique <strong>Customer ID</strong> and access your personal shipment dashboard.</p>
            <ul>
                <li><strong>Dashboard</strong> — your overview: active shipments, recent orders and pending invoices.</li>
                <li><strong>Real-time Tracking</strong> — enter your Serial Number to view the live timeline of any shipment.</li>
                <li><strong>My Shipments</strong> — every shipment you've created.</li>
                <li><strong>New Request</strong> — submit a shipping request (Step 05).</li>
                <li><strong>Invoices &amp; Payments</strong> — view and pay online.</li>
            </ul>
            <div class="success-note">
                <strong>Your Customer ID / Shipping Mark</strong> (e.g. <span class="code-chip">ZMFFL 123456</span>) is auto-generated for you on the <strong>New Request</strong> page — you don't need to invent a code.
            </div>
        </div>
    </div>

    <!-- Step 02: Get your China warehouse address -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-warehouse"></i></div>
            <div>
                <span class="step-num">02</span>
                <h3>Get Your China Warehouse Address</h3>
                <p>Send your goods to our China facility</p>
            </div>
        </div>
        <div class="step-body">
            <p>Use your assigned <strong>Forus Freight China warehouse address</strong> and <strong>Shipping Mark</strong> when ordering from your supplier:</p>
            <div class="address-box">
                <strong>{{ config('forus.forwarding_address.name') }}</strong><br>
                {{ config('forus.forwarding_address.line1') }}<br>
                {{ config('forus.forwarding_address.line2') }}<br>
                {{ config('forus.forwarding_address.line3') }}<br>
                {{ config('forus.forwarding_address.country') }}<br>
                <span style="color:#64748b;">{{ config('forus.forwarding_address.address_cn') }}</span><br>
                <span style="color:#64748b;">📞 {{ config('forus.forwarding_address.phone') }}</span><br>
                <span style="color:#64748b;">✉️ {{ config('forus.forwarding_address.email') }}</span>
            </div>
            <div class="danger-note">
                <i class="fas fa-triangle-exclamation" style="margin-right: 0.5rem;"></i>
                <strong>Always</strong> ensure your <strong>Shipping Mark ZMFFL</strong> is clearly marked on your packages, together with your name and contact details. This allows us to identify and allocate your goods to your account quickly.
            </div>
            <p style="margin-top: 0.75rem;">Example of a correctly labelled parcel:</p>
            <div class="address-box" style="border-left-color: #22c55e;">
                <span class="code-chip">ZMFFL 406118</span>  <em style="color:#64748b;">← Shipping Mark + Customer ID</em><br>
                Grace Milumbe &nbsp;•&nbsp; +260 977 633 650<br>
                Forus Freight Warehouse, B18, Lijin Logistics Park<br>
                No.3 Yanjiang Road, Dabu, Lishui Town, Nanhai District, Foshan City, China
            </div>
        </div>
    </div>

    <!-- Step 03: We receive & verify -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <span class="step-num">03</span>
                <h3>We Receive &amp; Verify Your Goods</h3>
                <p>Your shipment is received at our China warehouse</p>
            </div>
        </div>
        <div class="step-body">
            <p>Once your goods arrive at our China warehouse, our team receives them against your <strong>Shipping Mark / Customer ID</strong>. Your shipment status is updated to <strong>Received at China Warehouse</strong> and you are notified.</p>
        </div>
    </div>

    <!-- Step 04: We consolidate & prepare -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div>
                <span class="step-num">04</span>
                <h3>We Process &amp; Consolidate</h3>
                <p>Goods are checked, processed and consolidated</p>
            </div>
        </div>
        <div class="step-body">
            <p>Your goods are processed, consolidated with other cargo and prepared for shipment. Status updates you may see: <strong>Shipment Being Processed</strong>, <strong>Shipment Consolidated</strong>, then <strong>Ready for Shipment</strong>.</p>
        </div>
    </div>

    <!-- Step 05: Choose your shipping method -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-box-open"></i></div>
            <div>
                <span class="step-num">05</span>
                <h3>Choose Your Shipping Method</h3>
                <p>Select the option that suits your needs</p>
            </div>
        </div>
        <div class="step-body">
            <p>Through your Forus Freight account (or our team on your behalf), select your preferred shipping method and submit your <strong>Shipping Request</strong>.</p>
            <ul>
                <li><strong>Client Information</strong> — your name and phone number.</li>
                <li><strong>Route &amp; Port Details</strong> — origin (e.g. Guangzhou) and destination (Zambia).</li>
                <li><strong>Shipment Details</strong> — service type, shipping method and dates. Your <strong>Customer ID</strong> and <strong>Serial Number</strong> are auto-generated.</li>
                <li><strong>Cargo Details</strong> — description, number of parcels, volume (CBM) and gross weight.</li>
                <li><strong>Images</strong> — optional photos of the cargo or packaging.</li>
            </ul>
            <a href="{{ route('client.shipments.create') }}" style="display: inline-block; margin-top: 0.75rem; color: #007f7f; font-weight: 800; text-decoration: none;">
                <i class="fas fa-plus"></i> Submit a Shipping Request →
            </a>
        </div>
    </div>

    <!-- Step 06: Shipment departs China -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-ship"></i></div>
            <div>
                <span class="step-num">06</span>
                <h3>Your Shipment Departs China</h3>
                <p>On its way to Zambia</p>
            </div>
        </div>
        <div class="step-body">
            <p>When your cargo leaves our China warehouse you will see <strong>Departed China</strong>, followed by <strong>In Transit to Zambia</strong> while your goods are on the water or in the air.</p>
        </div>
    </div>

    <!-- Step 07: Arrival & customs clearance -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-shield-halved"></i></div>
            <div>
                <span class="step-num">07</span>
                <h3>Arrival &amp; Customs Clearance</h3>
                <p>Your shipment arrives in Zambia</p>
            </div>
        </div>
        <div class="step-body">
            <p>Your shipment arrives in Zambia (<strong>Arrived in Zambia</strong>) and goes through <strong>Customs Clearance</strong>. Once cleared, the status becomes <strong>Customs Cleared</strong> and your goods are ready for the final step.</p>
        </div>
    </div>

    <!-- Step 08: Ready for collection / delivery -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-truck-ramp-box"></i></div>
            <div>
                <span class="step-num">08</span>
                <h3>Ready for Collection / Delivery</h3>
                <p>Final leg to you</p>
            </div>
        </div>
        <div class="step-body">
            <p>You will see <strong>Ready for Collection</strong> when your goods await pickup, or <strong>Out for Delivery</strong> when they are on the final journey to your door.</p>
        </div>
    </div>

    <!-- Step 09: Track every stage -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-location-crosshairs"></i></div>
            <div>
                <span class="step-num">09</span>
                <h3>Track Every Stage</h3>
                <p>Real-time visibility from China to Zambia</p>
            </div>
        </div>
        <div class="step-body">
            <p>Monitor your shipment at every stage — through <strong>Real-time Tracking</strong> in the sidebar, or the public page at <a href="{{ route('track') }}" style="color:#007f7f;">/track</a>.</p>
            <ul>
                <li>Enter your <strong>Serial Number</strong> (e.g. <span class="code-chip">DUR.37977</span>), your auto-generated <strong>Customer ID</strong> (e.g. <span class="code-chip">ZMFFL 123456</span>), or a carrier <strong>Tracking Number</strong>.</li>
                <li>See the current status with its customer label, origin → destination, estimated delivery and a live stage timeline.</li>
            </ul>
            <div class="address-box" style="border-left-color: #ff6200;">
                <strong style="color:#1e293b;">Where each stage shows up on your timeline</strong><br>
                <span style="color:#475569;">Shipment Created → Received at China Warehouse → Processing → Consolidated → Ready for Shipment → Departed China → In Transit to Zambia → Arrived in Zambia → Customs Clearance → Ready for Collection → Delivered</span>
            </div>
        </div>
    </div>

    <!-- Step 10: Receive your goods -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon"><i class="fas fa-hand-holding-box"></i></div>
            <div>
                <span class="step-num">10</span>
                <h3>Receive Your Goods</h3>
                <p>Delivered to you, wherever you are</p>
            </div>
        </div>
        <div class="step-body">
            <p>Once delivered, your shipment is marked <strong>Delivered</strong>. You can arrange <strong>collection</strong> or <strong>door-to-door delivery</strong>, depending on your selected service. In the rare event of a delay, you may see <strong>Shipment on Hold</strong> or <strong>Shipment Exception</strong>, and our team will follow up with you.</p>
        </div>
    </div>

    <!-- Why ship with Forus -->
    <div class="walkthrough-card">
        <div class="step-tag">
            <div class="step-icon" style="background: #fff7ed; color: #ff6200;"><i class="fas fa-star"></i></div>
            <div>
                <h3>Why Ship With Forus Freight?</h3>
                <p>Built for cross-border moving</p>
            </div>
        </div>
        <div class="benefits-grid">
            <div class="benefit-item"><i class="fas fa-check"></i><p>Transparent Process — know where your shipment is at each stage.</p></div>
            <div class="benefit-item"><i class="fas fa-check"></i><p>Shipment Visibility — access shipment information through your customer account.</p></div>
            <div class="benefit-item"><i class="fas fa-check"></i><p>China-Based Consolidation — we receive and consolidate your goods at our China facility.</p></div>
            <div class="benefit-item"><i class="fas fa-check"></i><p>Flexible Shipping Options — choose sea or air freight according to your requirements.</p></div>
            <div class="benefit-item"><i class="fas fa-check"></i><p>Customs &amp; Logistics Support — we coordinate the journey from China to Zambia.</p></div>
            <div class="benefit-item"><i class="fas fa-check"></i><p>Door-to-Door Solutions — convenient delivery options for eligible shipments.</p></div>
        </div>
    </div>

    <!-- Ready to ship -->
    <div style="background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%); border-radius: 24px; padding: 2.5rem; margin-bottom: 1.5rem; color: white; text-align: center;">
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.75rem;">Ready to Ship?</h2>
        <p style="font-size: 0.9rem; opacity: 0.9; max-width: 560px; margin: 0 auto 1.5rem;">Create your Forus Freight account today and start shipping from China to Zambia — lifting lives today for a better tomorrow.</p>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
            <a href="{{ route('client.shipments.create') }}" style="text-decoration: none; background: #ff6200; color: white; padding: 0.9rem 1.75rem; border-radius: 14px; font-weight: 800; font-size: 0.9rem;">
                <i class="fas fa-plus"></i> CREATE YOUR SHIPMENT
            </a>
            <a href="{{ route('client.help') }}" style="text-decoration: none; background: rgba(255,255,255,0.15); color: white; padding: 0.9rem 1.75rem; border-radius: 14px; font-weight: 800; font-size: 0.9rem;">
                <i class="fas fa-warehouse"></i> GET CHINA WAREHOUSE ADDRESS
            </a>
            <a href="{{ route('client.tracking.auto') }}" style="text-decoration: none; background: rgba(255,255,255,0.15); color: white; padding: 0.9rem 1.75rem; border-radius: 14px; font-weight: 800; font-size: 0.9rem;">
                <i class="fas fa-location-crosshairs"></i> TRACK MY SHIPMENT
            </a>
        </div>
    </div>

    <p style="text-align: center; font-size: 0.75rem; color: #94a3b8; margin-top: 1rem;">
        Shipping rates are subject to change based on prevailing carrier, fuel, exchange-rate and logistics costs. Applicable rates will be confirmed at the time of shipment.
    </p>
</div>
@endsection