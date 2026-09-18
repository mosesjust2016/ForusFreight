@extends('layouts.dashboard')

@section('title', 'My Shipments - Forus Freight')

@section('styles')
<style>
    .shipment-table-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 1.25rem 1rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        border-bottom: 2px solid #f8fafc;
        font-weight: 800;
    }

    td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .tracking-id {
        font-family: 'JetBrains Mono', monospace;
        background: #f1f5f9;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        color: #475569;
    }

    .route-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .route-icon {
        color: #94a3b8;
        font-size: 0.8rem;
    }

    .status-pill {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-transit { background: #e0f2fe; color: #0369a1; }
    .status-delivered { background: #f0fdf4; color: #15803d; }
    .status-pending { background: #fefce8; color: #a16207; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
    }

    .action-btn:hover {
        background: #007f7f;
        color: white;
        border-color: #007f7f;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state img {
        width: 240px;
        margin-bottom: 2rem;
        opacity: 0.6;
    }

</style>
@endsection

@section('content')
<div class="welcome-section" style="display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">My Shipments</h1>
        <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Manage and track your global freight operations.</p>
    </div>
    <a href="{{ route('client.shipments.create') }}" class="btn-create" style="margin-bottom: 0.5rem;">
        <i class="fas fa-plus"></i> New Request
    </a>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1.25rem; border-radius: 16px; margin-bottom: 2.5rem; color: #15803d; font-weight: 600; display: flex; align-items: center; gap: 1rem; animation: slideIn 0.3s ease;">
        <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
        {{ session('success') }}
    </div>
@endif

<div class="shipment-table-card">
    @if($shipments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>Estimated Delivery</th>
                    <th>Total Cost</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $shipment)
                <tr>
                    <td>
                        <span class="tracking-id">{{ $shipment->serial_no }}</span>
                    </td>
                    <td>
                        <div class="route-info">
                            <div style="text-align: left;">
                                <div style="font-size: 0.9rem;">{{ $shipment->origin }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Origin</div>
                            </div>
                            <i class="fas fa-arrow-right route-icon"></i>
                            <div style="text-align: left;">
                                <div style="font-size: 0.9rem;">{{ $shipment->destination }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Destination</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $statusClass = 'status-pending';
                            if($shipment->status == 'Delivered') $statusClass = 'status-delivered';
                            elseif(in_array($shipment->status, ['In Transit', 'Out for Delivery'])) $statusClass = 'status-transit';
                            elseif($shipment->status == 'Cancelled') $statusClass = 'status-cancelled';
                        @endphp
                        <span class="status-pill {{ $statusClass }}">
                            <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                            {{ $shipment->status }}
                        </span>
                    </td>
                    <td>
                        @if($shipment->estimated_delivery)
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="far fa-calendar-alt" style="color: #94a3b8;"></i>
                                {{ $shipment->estimated_delivery->format('M d, Y') }}
                            </div>
                        @else
                            <span style="color: #94a3b8; font-weight: 500;">Pending Update</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight: 800; color: #1e293b;">ZMW {{ number_format($shipment->cost, 2) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('tracking.show', $shipment->serial_no) }}" class="action-btn" title="Track Shipment">
                            <i class="fas fa-location-dot"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; text-align: center;">
            {{ $shipments->links('vendor.pagination.bootstrap-5') }}
        </div>
    @else
        <div class="empty-state">
            <img src="https://illustrations.popsy.co/emerald/shipping.svg" alt="No shipments">
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem;">No Shipments Yet</h3>
            <p style="color: #64748b; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">Start your first global freight journey with Forus Freight today.</p>
            <a href="{{ route('client.shipments.create') }}" class="btn-create" style="display: inline-flex;">
                <i class="fas fa-plus"></i> Request New Shipment
            </a>
        </div>
    @endif
</div>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
