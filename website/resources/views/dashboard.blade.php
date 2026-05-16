@extends('layouts.dashboard')

@section('title', 'Client Dashboard - Forus Freight')

@section('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-shipments { background: #e0f2fe; color: #0ea5e9; }
    .icon-spent { background: #f0fdf4; color: #22c55e; }
    .icon-pending { background: #fff7ed; color: #f97316; }
    .icon-total { background: #f5f3ff; color: #8b5cf6; }

    .stat-info p {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .dashboard-section {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
    }

    .view-all {
        font-size: 0.85rem;
        color: #007f7f;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Shipments List */
    .shipment-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .shipment-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .shipment-item:hover {
        border-color: #007f7f33;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .shipment-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .shipment-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 1.2rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .shipment-details h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }

    .shipment-details p {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-transit { background: #e0f2fe; color: #0369a1; }
    .status-delivered { background: #f0fdf4; color: #15803d; }
    .status-pending { background: #fefce8; color: #a16207; }

    /* Payment Cards */
    .payment-card {
        padding: 1.25rem;
        border-radius: 16px;
        background: #f8fafc;
        margin-bottom: 1rem;
    }

    .payment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .payment-amount {
        font-weight: 800;
        color: #1e293b;
    }

    .payment-date {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .pay-btn {
        width: 100%;
        margin-top: 1rem;
        padding: 0.75rem;
        border-radius: 12px;
        background: #ff6200;
        color: white;
        border: none;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .pay-btn:hover {
        background: #e65800;
        transform: translateY(-2px);
    }

    /* Tracking Section */
    .tracking-panel {
        background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%);
        border: 2px solid #007f7f;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2.5rem;
    }

    .tracking-search {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .tracking-search input {
        flex: 1;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        border: 2px solid #007f7f33;
        font-size: 1rem;
        font-family: inherit;
        outline: none;
        transition: all 0.3s;
    }

    .tracking-search input:focus {
        border-color: #007f7f;
        box-shadow: 0 0 0 4px rgba(0,127,127,0.1);
    }

    .tracking-search button {
        padding: 1rem 2rem;
        border-radius: 14px;
        background: #ff6200;
        color: white;
        border: none;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tracking-search button:hover {
        background: #e65800;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255,98,0,0.3);
    }

    .tracking-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .tracking-timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #cccccc;
    }

    .timeline-event {
        position: relative;
        margin-bottom: 1.75rem;
    }

    .timeline-event:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -26px;
        top: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #007f7f;
        border: 3px solid #e0f2f2;
    }

    .timeline-dot.latest {
        background: #ff6200;
        border-color: #fff0e6;
    }

    .tracking-meta {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .tracking-meta-item p {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .tracking-meta-item h4 {
        font-size: 1.1rem;
        color: #1e293b;
        font-weight: 700;
    }

    @media (max-width: 1280px) {
        .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .content-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
    <div class="welcome-section">
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
        <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Here's what's happening with your shipments today.</p>
    </div>

    <!-- Real-time Tracking Panel -->
    <div class="tracking-panel">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #1e293b;"><i class="fas fa-location-crosshairs" style="color: #007f7f; margin-right: 0.5rem;"></i>Real-time Tracking</h2>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="tracking-search">
            <input type="text" name="tracking_number" placeholder="Enter tracking number (e.g. FORUS-ZM-1001)" value="{{ request('tracking_number') }}" required>
            <button type="submit"><i class="fas fa-search"></i> Track Shipment</button>
        </form>

        @if($trackingError)
            <div style="padding: 1rem; background: rgba(239,68,68,0.08); border-radius: 12px; border-left: 4px solid #ef4444;">
                <p style="color: #ef4444; margin: 0; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-circle-exclamation" style="margin-right: 0.5rem;"></i>{{ $trackingError }}</p>
            </div>
        @endif

        @if($trackedShipment)
            <div style="background: white; border-radius: 20px; padding: 2rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <p style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 0.25rem;">Tracking Number</p>
                        <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">{{ $trackedShipment->tracking_number }}</h3>
                    </div>
                    <span style="padding: 0.5rem 1.25rem; background: #007f7f; color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 700;">
                        {{ strtoupper($trackedShipment->status) }}
                    </span>
                </div>

                <div class="tracking-meta">
                    <div class="tracking-meta-item">
                        <p>From</p>
                        <h4>{{ $trackedShipment->origin }}</h4>
                    </div>
                    <div class="tracking-meta-item">
                        <p>To</p>
                        <h4>{{ $trackedShipment->destination }}</h4>
                    </div>
                    <div class="tracking-meta-item">
                        <p>Shipped</p>
                        <h4>{{ $trackedShipment->shipment_date ? $trackedShipment->shipment_date->format('M d, Y') : 'N/A' }}</h4>
                    </div>
                    <div class="tracking-meta-item">
                        <p>Est. Delivery</p>
                        <h4>{{ $trackedShipment->estimated_delivery ? $trackedShipment->estimated_delivery->format('M d, Y') : 'N/A' }}</h4>
                    </div>
                </div>

                @if($trackedShipment->trackingEvents->count() > 0)
                    <h4 style="font-weight: 700; color: #1e293b; margin-bottom: 1.25rem; font-size: 1rem;">Shipment Timeline</h4>
                    <div class="tracking-timeline">
                        @foreach($trackedShipment->trackingEvents as $index => $event)
                            <div class="timeline-event">
                                <div class="timeline-dot {{ $index == count($trackedShipment->trackingEvents) - 1 ? 'latest' : '' }}"></div>
                                <p style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem; font-size: 0.95rem;">{{ $event->description }}</p>
                                <p style="color: #64748b; font-size: 0.85rem;">
                                    {{ $event->location }} • {{ $event->event_time->format('M d, Y • h:i A') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 0;">
                        <i class="fas fa-box-open" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <p style="color: #64748b; font-weight: 600;">No tracking events available yet.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon icon-shipments">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div class="stat-info">
                <p>Active Shipments</p>
                <h3>{{ $stats['active_shipments'] }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-spent">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <p>Total Paid</p>
                <h3>ZMW {{ number_format($stats['total_spent'], 2) }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-pending">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-info">
                <p>Pending Invoices</p>
                <h3>{{ $stats['pending_invoices'] }}</h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-total">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div class="stat-info">
                <p>Total Orders</p>
                <h3>{{ $stats['total_shipments'] }}</h3>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Recent Shipments -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Recent Shipments</h2>
                <a href="{{ route('client.shipments') }}" class="view-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="shipment-list">
                @forelse($recentShipments as $shipment)
                    <div class="shipment-item">
                        <div class="shipment-info">
                            <div class="shipment-avatar">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="shipment-details">
                                <h4>{{ $shipment->tracking_number }}</h4>
                                <p>{{ $shipment->origin }} → {{ $shipment->destination }}</p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="status-badge {{ strtolower(str_replace(' ', '-', $shipment->status)) == 'delivered' ? 'status-delivered' : (in_array($shipment->status, ['Order Placed', 'Pending']) ? 'status-pending' : 'status-transit') }}">
                                {{ $shipment->status }}
                            </span>
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                                {{ $shipment->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem 0;">
                        <img src="https://illustrations.popsy.co/emerald/delivery-truck.svg" alt="No shipments" style="width: 200px; margin-bottom: 1.5rem; opacity: 0.5;">
                        <p style="color: #64748b; font-weight: 600;">No shipments found yet.</p>
                        <a href="{{ route('client.shipments.create') }}" style="color: #007f7f; font-size: 0.9rem; font-weight: 700; text-decoration: none; display: block; margin-top: 1rem;">Start your first shipment →</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Pending Payments</h2>
            </div>

            @forelse($pendingPayments as $payment)
                <div class="payment-card">
                    <div class="payment-header">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">{{ $payment->invoice_number }}</span>
                        <span class="payment-amount">ZMW {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #475569; margin: 0.5rem 0;">Shipment: {{ $payment->shipment->tracking_number }}</p>
                    <span class="payment-date">Due: {{ $payment->due_date->format('M d, Y') }}</span>
                    <button class="pay-btn">Pay Now</button>
                </div>
            @empty
                <div style="text-align: center; padding: 2rem 0;">
                    <div style="width: 64px; height: 64px; background: #f0fdf4; color: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p style="color: #64748b; font-weight: 600; font-size: 0.9rem;">You're all caught up!</p>
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">No pending invoices found.</p>
                </div>
            @endforelse

            <!-- Help Support Card -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%); border-radius: 20px; color: white; position: relative; overflow: hidden;">
                <i class="fas fa-headset" style="position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
                <h4 style="font-weight: 800; margin-bottom: 0.5rem;">Need Help?</h4>
                <p style="font-size: 0.8rem; opacity: 0.9; margin-bottom: 1.25rem;">Our support team is available 24/7 for your logistics needs.</p>
                <a href="{{ route('client.help') }}" style="display: inline-block; padding: 0.6rem 1.25rem; background: white; color: #007f7f; border-radius: 12px; font-size: 0.8rem; font-weight: 800; text-decoration: none;">Contact Support</a>
            </div>
        </div>
    </div>
@endsection
