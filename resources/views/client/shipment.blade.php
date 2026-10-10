@extends('layouts.dashboard')

@section('title', $shipment->serial_no . ' - Forus Freight')

@section('styles')
<style>
    .sd-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 40px;
        margin-bottom: 1rem;
        color: var(--ink-2);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .sd-back:hover { color: var(--ink); }

    .sd-help {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 1.25rem;
        padding: 1.1rem 1.4rem;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        color: var(--ink-2);
        font-size: 0.95rem;
    }

    .sd-help a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 42px;
        padding: 0 1rem;
        border-radius: 10px;
        border: 1px solid var(--line-strong);
        color: var(--ink);
        font-weight: 600;
        text-decoration: none;
    }

    .sd-help a:hover { border-color: var(--ink); }
</style>
@endsection

@section('content')
    <a href="{{ route('client.shipments') }}" class="sd-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Shipments</a>

    @include('partials.shipment-card', ['shipment' => $shipment, 'ref' => $shipment->serial_no])

    <div class="sd-help">
        <span>Question about this shipment?</span>
        <a href="https://wa.me/260572788685?text={{ urlencode('Hi Forus Freight, I have a question about shipment ' . $shipment->serial_no) }}" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp" aria-hidden="true"></i> Ask on WhatsApp
        </a>
    </div>
@endsection
