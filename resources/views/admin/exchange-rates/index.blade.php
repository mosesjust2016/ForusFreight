@extends('layouts.dashboard')

@section('title', 'Exchange Rates & Hedging - Forus Freight')

@section('styles')
<style>
    .rate-card {
        background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%);
        color: white;
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .rate-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .rate-value {
        font-size: 3rem;
        font-weight: 900;
        margin: 0.5rem 0;
    }
    .rate-meta {
        opacity: 0.85;
        font-size: 0.9rem;
    }
    .rate-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .rate-stat {
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
        backdrop-filter: blur(10px);
    }
    .rate-stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.8;
        margin-bottom: 0.5rem;
    }
    .rate-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
    }
    .section-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
        overflow-x: auto;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
    }
    .btn-primary-sm {
        background: #007f7f;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-primary-sm:hover {
        background: #005f5f;
        transform: translateY(-2px);
    }
    .btn-orange {
        background: #ff6200;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-orange:hover {
        background: #e65800;
        transform: translateY(-2px);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        text-align: left;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
    }
    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.9rem;
        color: #1e293b;
    }
    .data-table tr:hover td {
        background: #f8fafc;
    }
    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-expired { background: #fee2e2; color: #991b1b; }
    .status-utilized { background: #dbeafe; color: #1e40af; }
    .status-cancelled { background: #f3f4f6; color: #4b5563; }
    .trend-up { color: #22c55e; }
    .trend-down { color: #ef4444; }

    @media (max-width: 900px) {
        .rate-grid { grid-template-columns: repeat(2, 1fr); }
        div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        .rate-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Exchange Rates & Hedging</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Manage BOZ exchange rates and client currency hedges.</p>
    </div>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border-left: 4px solid #16a34a; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; color: #166534; font-weight: 700;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; color: #991b1b; font-weight: 700;">
        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
    </div>
@endif

<!-- Current Rate Card -->
<div class="rate-card">
    <div style="display: flex; justify-content: space-between; align-items: start; position: relative; z-index: 1;">
        <div>
            <div class="rate-meta"><i class="fas fa-building-columns" style="margin-right: 0.5rem;"></i>Bank of Zambia (BOZ)</div>
            @if($latestRate)
                <div class="rate-value">{{ number_format($latestRate->mid_rate, 4) }} <span style="font-size: 1.25rem; font-weight: 600;">ZMW</span></div>
                <div class="rate-meta">1 USD = {{ number_format($latestRate->mid_rate, 4) }} ZMW (Mid Rate)</div>
                <div class="rate-meta" style="margin-top: 0.5rem; font-size: 0.8rem; opacity: 0.7;">
                    <i class="far fa-clock"></i> Last updated: {{ $latestRate->recorded_at->format('d M Y H:i') }}
                </div>
            @else
                <div class="rate-value">--</div>
                <div class="rate-meta">No exchange rate data available</div>
            @endif
        </div>
        <form action="{{ route('admin.exchange-rates.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary-sm" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                <i class="fas fa-sync-alt"></i> Sync from BOZ
            </button>
        </form>
    </div>

    @if($latestRate)
    <div class="rate-grid">
        <div class="rate-stat">
            <div class="rate-stat-label">Buying Rate</div>
            <div class="rate-stat-value">{{ number_format($latestRate->buying_rate, 4) }}</div>
        </div>
        <div class="rate-stat">
            <div class="rate-stat-label">Mid Rate</div>
            <div class="rate-stat-value">{{ number_format($latestRate->mid_rate, 4) }}</div>
        </div>
        <div class="rate-stat">
            <div class="rate-stat-label">Selling Rate</div>
            <div class="rate-stat-value">{{ number_format($latestRate->selling_rate, 4) }}</div>
        </div>
    </div>
    @endif
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Rate History -->
    <div class="section-card">
        <div class="section-header">
            <h2><i class="fas fa-chart-line" style="color: #007f7f; margin-right: 0.5rem;"></i>Rate History</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Buying</th>
                    <th>Mid</th>
                    <th>Selling</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rates as $rate)
                <tr>
                    <td>{{ $rate->recorded_at->format('d M Y H:i') }}</td>
                    <td>{{ number_format($rate->buying_rate, 4) }}</td>
                    <td><strong>{{ number_format($rate->mid_rate, 4) }}</strong></td>
                    <td>{{ number_format($rate->selling_rate, 4) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 2rem;">No exchange rates recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">
            {{ $rates->links() }}
        </div>
    </div>

    <!-- Currency Hedges -->
    <div class="section-card">
        <div class="section-header">
            <h2><i class="fas fa-shield-halved" style="color: #ff6200; margin-right: 0.5rem;"></i>Currency Hedges</h2>
            <a href="{{ route('admin.exchange-rates.hedge.create') }}" class="btn-orange">
                <i class="fas fa-plus"></i> New Hedge
            </a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Amount (USD)</th>
                    <th>Rate</th>
                    <th>ZMW</th>
                    <th>Expiry</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hedges as $hedge)
                <tr>
                    <td>{{ $hedge->user->name }}</td>
                    <td>${{ number_format($hedge->amount_usd, 2) }}</td>
                    <td>{{ number_format($hedge->hedged_rate, 4) }}</td>
                    <td>ZMW {{ number_format($hedge->amount_zmw, 2) }}</td>
                    <td>{{ $hedge->expiry_date->format('d M Y') }}</td>
                    <td>
                        <span class="status-badge status-{{ $hedge->status }}">{{ ucfirst($hedge->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">No hedges created yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">
            {{ $hedges->links() }}
        </div>
    </div>
</div>
@endsection
