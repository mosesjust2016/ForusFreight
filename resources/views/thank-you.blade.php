@php
$type = request()->query('type');
$responseTime = $type === 'quote' ? '2 hours' : '24 hours';
@endphp
@extends('layouts.document')

@section('title', 'Thank You - Forus Freight')
@section('meta_description', 'Your enquiry has been received by Forus Freight.')
@section('robots', 'noindex, follow')

@section('content')
<section style="min-height: 100vh; display: flex; align-items: center; padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 640px; margin: auto; text-align: center;">
            <div style="width: 96px; height: 96px; margin: 0 auto 2rem; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-circle-check" style="font-size: 2.5rem; color: rgb(0,127,127);" aria-hidden="true"></i>
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 900; color: #1e293b; margin-bottom: 1rem;">Thank You — We've Received Your {{ $type === 'quote' ? 'Quote Request' : 'Message' }}</h1>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 1rem; line-height: 1.7;">
                A member of the Forus Freight team will review the details you submitted and get back to you within <strong>{{ $responseTime }}</strong>.
            </p>
            <p style="color: #64748b; font-size: .95rem; margin-bottom: 2.5rem; line-height: 1.7;">
                In the meantime, feel free to explore our services or track an existing shipment.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem;">
                <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: .5rem; background: rgb(0,127,127); color: #fff; padding: .9rem 1.75rem; border-radius: 12px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-house" aria-hidden="true"></i> Back to Homepage
                </a>
                <a href="{{ route('tracking') }}" style="display: inline-flex; align-items: center; gap: .5rem; background: #fff; color: rgb(0,127,127); border: 2px solid rgb(0,127,127); padding: .9rem 1.75rem; border-radius: 12px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-location-crosshairs" aria-hidden="true"></i> Track a Shipment
                </a>
            </div>
            <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; font-size: .9rem;">
                <a href="{{ route('services') }}" style="color: #475569; text-decoration: none; font-weight: 600;">Our Services</a>
                <a href="{{ route('faq') }}" style="color: #475569; text-decoration: none; font-weight: 600;">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
