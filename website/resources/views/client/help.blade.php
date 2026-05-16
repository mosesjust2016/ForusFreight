@extends('layouts.dashboard')

@section('title', 'Help Center - Forus Freight')

@section('styles')
<style>
    .help-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .help-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        text-align: center;
        transition: all 0.3s;
        text-decoration: none;
    }

    .help-card:hover {
        transform: translateY(-5px);
        border-color: #007f7f;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .help-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: #f0f9f9;
        color: #007f7f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1.5rem;
    }

    .help-card h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }

    .help-card p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
    }

    .faq-section {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }

    .faq-item {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #f8fafc;
    }

    .faq-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .faq-item h4 {
        font-size: 1rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .faq-item h4 i { color: #007f7f; }

    .faq-item p {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        padding-left: 1.75rem;
    }

    @media (max-width: 768px) {
        .help-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Help Center</h1>
    <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Need assistance? We're here to help you 24/7.</p>
</div>

<div class="help-grid">
    <a href="#" class="help-card">
        <div class="help-icon"><i class="fas fa-book-open"></i></div>
        <h3>Guides & FAQ</h3>
        <p>Explore our detailed guides on logistics, border protocols, and shipping regulations.</p>
    </a>
    <a href="#" class="help-card">
        <div class="help-icon" style="background: #eef2ff; color: #4f46e5;"><i class="fas fa-comments"></i></div>
        <h3>Live Support</h3>
        <p>Chat with our dedicated dispatch team for real-time updates and urgent inquiries.</p>
    </a>
    <a href="mailto:support@forusfreight.com" class="help-card">
        <div class="help-icon" style="background: #fff7ed; color: #ea580c;"><i class="fas fa-envelope-open-text"></i></div>
        <h3>Email Ticket</h3>
        <p>Open a support ticket via email and our regional managers will respond within 2 hours.</p>
    </a>
</div>

<div class="faq-section">
    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2.5rem; color: #1e293b;">Common Questions</h3>
    
    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> How do I track my shipment?</h4>
        <p>Go to "Real-time Tracking" in your sidebar and enter your tracking number. You'll see live location data and current border status updates.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> What documents do I need to upload?</h4>
        <p>For most regional shipments, you'll need the Bill of Lading, Commercial Invoice, and Packing List. You can upload these when creating a request.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> How can I settle outstanding invoices?</h4>
        <p>Visit the "Invoices & Payments" section. You can view all pending balances and pay directly using our integrated payment gateway.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> What areas do you operate in?</h4>
        <p>We provide freight solutions across Zambia and the entire SADC region, including South Africa, DRC, Zimbabwe, and Tanzania.</p>
    </div>
</div>
@endsection
