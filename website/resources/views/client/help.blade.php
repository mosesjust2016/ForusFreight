@extends('layouts.dashboard')

@section('title', 'Help Center - Forus Freight')

@section('styles')
<style>
    .help-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-bottom: 4rem;
    }

    .help-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        text-align: center;
        transition: all 0.3s;
        text-decoration: none;
    }

    .help-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary-green);
    }

    .help-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1.5rem;
    }

    .help-card h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .help-card p {
        font-size: 0.85rem;
        color: var(--text-gray);
        line-height: 1.5;
    }

    .faq-section {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
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
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .faq-item h4 i { color: var(--primary-green); }

    .faq-item p {
        font-size: 0.9rem;
        color: var(--text-gray);
        line-height: 1.6;
        padding-left: 1.75rem;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Help & Support Center</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Need assistance with your cargo or account? We are here to help 24/7.</p>
    </div>
</div>

<div class="help-grid">
    <a href="#" class="help-card">
        <div class="help-icon"><i class="fas fa-book"></i></div>
        <h3>Knowledge Base</h3>
        <p>Explore our guides on shipping regulations, documentation, and border protocols.</p>
    </a>
    <a href="#" class="help-card">
        <div class="help-icon" style="background: #eff6ff; color: #2563eb;"><i class="fas fa-message"></i></div>
        <h3>Live Chat</h3>
        <p>Speak directly with our dispatch team for real-time updates on your cargo.</p>
    </a>
    <a href="mailto:support@forusfl.co.zm" class="help-card">
        <div class="help-icon" style="background: #fff7ed; color: #c2410c;"><i class="fas fa-envelope"></i></div>
        <h3>Email Support</h3>
        <p>Send us a detailed inquiry and get a response within 2 working hours.</p>
    </a>
</div>

<div class="faq-section">
    <h3 style="font-size: 1.25rem; font-weight: 900; margin-bottom: 2.5rem; color: var(--text-dark);">Frequently Asked Questions</h3>
    
    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> How do I track my shipment in real-time?</h4>
        <p>You can use the "Real-time Tracking" link in your sidebar. Our system uses live telemetry to show your cargo's exact location and border status.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> What documents are required for international freight?</h4>
        <p>Typically, you will need a Commercial Invoice, Packing List, and Bill of Lading. You can upload these directly when creating a new shipment request.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> How long does border clearance usually take?</h4>
        <p>Clearance times vary by border. On average, regional borders like Kasumbalesa or Chirundu take 12-24 hours for standard general cargo.</p>
    </div>

    <div class="faq-item">
        <h4><i class="fas fa-circle-question"></i> Can I change my delivery destination after dispatch?</h4>
        <p>Yes, but please contact our support team immediately. Changes made after the vehicle has passed the final border may incur additional logistics fees.</p>
    </div>
</div>
@endsection
