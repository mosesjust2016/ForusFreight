@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Forus Freight')

@section('styles')
<style>
    .welcome-section {
        margin-bottom: 2rem;
    }

    .welcome-section h1 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .welcome-section p {
        color: var(--text-gray);
        font-size: 0.9rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(76, 175, 80, 0.05), transparent 70%);
        pointer-events: none;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-gray);
        text-align: right;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .stat-change {
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-change.up { color: var(--primary-green); }
    .stat-change.down { color: #ff5252; }

    .stat-chart {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 80px;
        height: 40px;
    }

    /* Main Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 2rem;
    }

    .recent-shipments {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
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
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .view-all {
        color: var(--text-gray);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: color 0.3s;
    }

    .view-all:hover { color: var(--primary-green); }

    /* Shipment Card */
    .shipment-card {
        border: 1.5px solid #f1f5f9;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: border-color 0.3s;
    }

    .shipment-card:hover {
        border-color: var(--primary-green);
    }

    .shipment-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .shipment-id {
        font-size: 1.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .shipment-status-tag {
        background: var(--primary-green-light);
        color: var(--primary-green);
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .shipment-info {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        color: var(--text-gray);
        font-size: 0.85rem;
    }

    .shipment-info item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Progress Tracker */
    .progress-wrapper {
        margin-top: 1rem;
        position: relative;
    }

    .progress-bar {
        height: 4px;
        background: #f1f5f9;
        border-radius: 10px;
        width: 100%;
        position: relative;
        margin-bottom: 1rem;
    }

    .progress-fill {
        position: absolute;
        height: 100%;
        background: var(--primary-green);
        border-radius: 10px;
        width: 60%; /* Dynamic based on status */
    }

    .progress-truck {
        position: absolute;
        top: -12px;
        left: 60%;
        transform: translateX(-50%);
        background: white;
        color: var(--primary-green);
        font-size: 1.2rem;
        transition: left 0.3s ease;
    }

    .progress-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-gray);
    }

    .progress-labels div {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .progress-labels div i {
        font-size: 0.85rem;
    }

    .progress-labels .active {
        color: var(--text-dark);
    }

    /* Right Sidebar */
    .right-side {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .map-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
    }

    .map-placeholder {
        width: 100%;
        height: 200px;
        background: #f8fafc url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg') center/contain no-repeat;
        border-radius: 16px;
        margin-top: 1rem;
        opacity: 0.4;
    }

    .status-list {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .status-item:last-child { border: none; }

    .status-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .status-id {
        font-weight: 700;
        font-size: 0.9rem;
        display: block;
    }

    .status-desc {
        font-size: 0.75rem;
        color: var(--text-gray);
    }

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Global Freight Overview - {{ now()->format('l jS M, Y') }}</p>
        </div>
        <div style="background: var(--primary-green); color: white; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.8rem;">
            SYSTEM ADMINISTRATOR
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-label">All Shipments</div>
        </div>
        <div class="stat-value">{{ $shipments->count() }}</div>
        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Global Total</div>
        <div class="stat-chart">
            <svg viewBox="0 0 100 40" class="mini-chart">
                <path d="M0,35 Q10,25 20,30 T40,20 T60,25 T80,10 T100,15" fill="none" stroke="var(--primary-green)" stroke-width="3" />
            </svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #fff8e1; color: #ff8f00;"><i class="fas fa-truck-ramp-box"></i></div>
            <div class="stat-label">Active Deliveries</div>
        </div>
        <div class="stat-value">{{ $shipments->whereIn('status', ['In Transit', 'Out for Delivery'])->count() }}</div>
        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Global Traffic</div>
        <div class="stat-chart">
            <svg viewBox="0 0 100 40" class="mini-chart">
                <rect x="0" y="20" width="10" height="15" fill="#ff8f00" rx="2" />
                <rect x="20" y="10" width="10" height="25" fill="#ff8f00" rx="2" />
                <rect x="40" y="25" width="10" height="10" fill="#ff8f00" rx="2" />
                <rect x="60" y="5" width="10" height="30" fill="#ff8f00" rx="2" />
                <rect x="80" y="15" width="10" height="20" fill="#ff8f00" rx="2" />
            </svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #e3f2fd; color: #1e88e5;"><i class="fas fa-route"></i></div>
            <div class="stat-label">Fleet Status</div>
        </div>
        <div class="stat-value">98%</div>
        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Optimal</div>
        <div class="stat-chart">
            <svg viewBox="0 0 100 40" class="mini-chart">
                <path d="M0,30 Q25,10 50,25 T100,15" fill="none" stroke="#1e88e5" stroke-width="3" />
            </svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #f3e5f5; color: #8e24aa;"><i class="fas fa-clock"></i></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-value">{{ $shipments->whereIn('status', ['Order Placed', 'Pending'])->count() }}</div>
        <div class="stat-change down"><i class="fas fa-arrow-down"></i> Action Required</div>
        <div class="stat-chart">
            <svg viewBox="0 0 100 40" class="mini-chart">
                <path d="M0,20 L20,30 L40,10 L60,35 L80,15 L100,25" fill="none" stroke="#8e24aa" stroke-width="3" />
            </svg>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Center Content -->
    <div class="recent-shipments">
        <div class="section-header">
            <h2><i class="fas fa-box-archive"></i> Global Shipment Registry</h2>
            <a href="{{ route('admin.shipments') }}" class="view-all">Manage All <i class="fas fa-arrow-right"></i></a>
        </div>

        @forelse($shipments as $shipment)
            @php
                $progress = match(strtolower($shipment->status)) {
                    'order placed' => 5,
                    'processing' => 30,
                    'in transit' => 60,
                    'out for delivery' => 90,
                    'delivered' => 100,
                    default => 0
                };
            @endphp
            <div class="shipment-card">
                <div class="shipment-top">
                    <div class="shipment-id">
                        #{{ $shipment->tracking_number }} 
                        <span class="shipment-status-tag">{{ $shipment->status }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">Client</span>
                        <span style="display: block; font-size: 0.85rem; font-weight: 700;">{{ $shipment->user ? $shipment->user->name : 'Guest' }}</span>
                    </div>
                </div>

                <div class="shipment-info">
                    <div style="display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-location-dot"></i> {{ $shipment->origin }} → {{ $shipment->destination }}</div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;"><i class="far fa-calendar"></i> ETA: {{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('M d') : 'TBD' }}</div>
                </div>

                <div class="progress-wrapper">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        <div class="progress-truck" style="left: {{ $progress }}%"><i class="fas fa-truck-fast"></i></div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 3rem 0; color: var(--text-gray);">
                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>No shipments in system.</p>
            </div>
        @endforelse
    </div>

    <!-- Right Side -->
    <div class="right-side">
        <div class="map-card">
            <div class="section-header" style="margin-bottom: 1rem;">
                <h2 style="font-size: 1.1rem;"><i class="fas fa-map-location-dot"></i> Network Traffic</h2>
            </div>
            <div class="map-placeholder"></div>
        </div>

        <div class="status-list">
            <h3 style="font-size: 0.9rem; margin-bottom: 1rem; color: var(--text-gray);">Recent Updates</h3>
            @foreach($shipments->take(5) as $shipment)
                <div class="status-item">
                    <div class="status-left">
                        <i class="fas fa-circle-dot" style="color: var(--primary-green); font-size: 0.5rem;"></i>
                        <div>
                            <span class="status-id">#{{ $shipment->tracking_number }}</span>
                            <span class="status-desc">{{ $shipment->status }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.shipments.edit', $shipment) }}" style="font-size: 0.7rem; color: var(--primary-green); font-weight: 700;">EDIT</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
