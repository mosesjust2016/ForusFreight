@extends('layouts.dashboard')

@section('title', 'Real-time Tracking - Forus Freight')

@section('styles')
<style>
    .tracking-search-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
    }

    .tracking-input-group {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .tracking-input {
        flex: 1;
        padding: 1rem 1.5rem;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s;
        background: #f8fafc;
    }

    .tracking-input:focus {
        outline: none;
        border-color: #007f7f;
        background: white;
    }

    .track-btn {
        padding: 1rem 2.5rem;
        background: #ff6200;
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(255, 98, 0, 0.2);
    }

    .track-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 98, 0, 0.3);
    }

    .shipment-details-card {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
    }

    .timeline {
        position: relative;
        padding-left: 2.5rem;
        margin-top: 3rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #f1f5f9;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2.5rem;
    }

    .timeline-dot {
        position: absolute;
        left: -2.5rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #e2e8f0;
        border: 4px solid white;
        box-shadow: 0 0 0 1px #e2e8f0;
        z-index: 1;
    }

    .timeline-item.active .timeline-dot {
        background: #ff6200;
        box-shadow: 0 0 0 1px #ff6200, 0 0 10px rgba(255, 98, 0, 0.4);
    }

    .timeline-content {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .timeline-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 1rem;
    }

    .timeline-time {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Real-time Tracking</h1>
    <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Get instant updates on your cargo's location and border status.</p>
</div>

<div class="tracking-search-card">
    <form action="{{ route('track.check') }}" method="POST">
        @csrf
        <label style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Shipment ID / Tracking Number</label>
        <div class="tracking-input-group">
            <input type="text" name="tracking_number" class="tracking-input" placeholder="e.g. FORUS-LUS-1234" value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}" required>
            <button type="submit" class="track-btn">
                <i class="fas fa-location-crosshairs"></i> Track Now
            </button>
        </div>
    </form>
</div>

@if(isset($shipment))
<div class="shipment-details-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid #f1f5f9;">
        <div>
            <span style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Current Status</span>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #1e293b; margin-top: 0.25rem;">{{ $shipment->status }}</h2>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Estimated Arrival</span>
            <p style="font-size: 1.25rem; font-weight: 800; color: #007f7f; margin-top: 0.25rem;">
                {{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('M d, Y') : 'Pending Update' }}
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 3rem;">
        <div>
            <p style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Origin</p>
            <p style="font-weight: 700; color: #1e293b;">{{ $shipment->origin }}</p>
        </div>
        <div style="text-align: center;">
            <i class="fas fa-truck-moving" style="color: #cbd5e1; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
            <div style="height: 2px; background: #f1f5f9; position: relative;">
                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 65%; background: #007f7f;"></div>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Destination</p>
            <p style="font-weight: 700; color: #1e293b;">{{ $shipment->destination }}</p>
        </div>
    </div>

    <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b;">Tracking History</h3>
    
    <div class="timeline">
        @forelse($shipment->trackingEvents()->latest()->get() as $index => $event)
            <div class="timeline-item {{ $index == 0 ? 'active' : '' }}">
                <div class="timeline-dot"></div>
                <div class="timeline-content" style="{{ $index == 0 ? 'background: #f0f9f9; border-color: #007f7f33;' : '' }}">
                    <div class="timeline-header">
                        <span class="timeline-title">{{ $event->description }}</span>
                        <span class="timeline-time">{{ $event->event_time->format('M d, h:i A') }}</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #64748b; font-weight: 500;">
                        <i class="fas fa-location-dot" style="margin-right: 0.25rem;"></i> {{ $event->location }}
                    </p>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 3rem 0; color: #94a3b8;">
                <p>No tracking events recorded yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endif
@endsection
