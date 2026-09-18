@extends('layouts.document')

@section('title', 'Page Not Found - Forus Freight')
@section('meta_description', 'The page you are looking for could not be found.')
@section('robots', 'noindex, follow')

@section('content')
<section style="min-height: 100vh; display: flex; align-items: center; padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 640px; margin: auto; text-align: center;">
            <div style="width: 96px; height: 96px; margin: 0 auto 2rem; border-radius: 50%; background: var(--primary-green-light, #e8f5e9); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-map-signs" style="font-size: 2.5rem; color: rgb(0,127,127);" aria-hidden="true"></i>
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 900; color: #1e293b; margin-bottom: 1rem;">Page Not Found</h1>
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 2.5rem; line-height: 1.7;">
                The page you're looking for doesn't exist, may have moved, or the link you followed may be outdated. Here's how to get back on track:
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem;">
                <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: .5rem; background: rgb(0,127,127); color: #fff; padding: .9rem 1.75rem; border-radius: 12px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-house" aria-hidden="true"></i> Go to Homepage
                </a>
                <a href="{{ route('contact') }}" style="display: inline-flex; align-items: center; gap: .5rem; background: #fff; color: rgb(0,127,127); border: 2px solid rgb(0,127,127); padding: .9rem 1.75rem; border-radius: 12px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-envelope" aria-hidden="true"></i> Contact Us
                </a>
            </div>
            <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; font-size: .9rem;">
                <a href="{{ route('services') }}" style="color: #475569; text-decoration: none; font-weight: 600;">Our Services</a>
                <a href="{{ route('tracking') }}" style="color: #475569; text-decoration: none; font-weight: 600;">Track a Shipment</a>
                <a href="{{ route('quote') }}" style="color: #475569; text-decoration: none; font-weight: 600;">Request a Quote</a>
                <a href="{{ route('faq') }}" style="color: #475569; text-decoration: none; font-weight: 600;">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
