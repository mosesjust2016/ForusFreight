@extends('layouts.dashboard')

@section('title', 'Global Shipment Management - Forus Freight')

@section('styles')
<style>
    .admin-shipment-grid {
        background: white;
        border-radius: 30px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .shipment-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 1rem;
    }

    .shipment-table th {
        text-align: left;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f8fafc;
    }

    .shipment-row {
        background: #fff;
        transition: all 0.3s;
    }

    .shipment-row:hover {
        background: #fcfdfe;
        transform: scale(1.002);
    }

    .shipment-row td {
        padding: 1.5rem 1rem;
        border-top: 1px solid #f8fafc;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .shipment-row td:first-child {
        border-left: 1px solid #f8fafc;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }

    .shipment-row td:last-child {
        border-right: 1px solid #f8fafc;
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .tracking-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        background: #f1f5f9;
        color: #475569;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .status-pill {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .status-pending { background: #fffbeb; color: #b45309; }
    .status-transit { background: #eff6ff; color: #1e40af; }
    .status-delivered { background: #f0fdf4; color: #166534; }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: #64748b;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
        background: #f8fafc;
        text-decoration: none;
    }

    .action-btn:hover {
        background: var(--primary-green);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Global Shipment Registry</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Comprehensive oversight of all cargo movements across the network.</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div style="background: white; padding: 0.75rem 1.5rem; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid #f1f5f9;">
                <span style="display: block; font-size: 0.65rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Active Freight</span>
                <span style="font-size: 1.25rem; font-weight: 900; color: var(--primary-green);">{{ $shipments->where('status', 'In Transit')->count() }}</span>
            </div>
            <div style="background: white; padding: 0.75rem 1.5rem; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid #f1f5f9;">
                <span style="display: block; font-size: 0.65rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Total Load</span>
                <span style="font-size: 1.25rem; font-weight: 900; color: var(--text-dark);">{{ $shipments->count() }}</span>
            </div>
            <a href="{{ route('admin.shipments.create') }}" style="background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border-radius: 15px; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i> New Shipment
            </a>
        </div>
    </div>
</div>

<div class="admin-shipment-grid">
    <table class="shipment-table">
        <thead>
            <tr>
                <th>Tracking ID</th>
                <th>Client / Customer</th>
                <th>Origin → Destination</th>
                <th>Cargo Details</th>
                <th>Financials</th>
                <th>Current Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $shipment)
                <tr class="shipment-row">
                    <td>
                        <span class="tracking-badge">#{{ $shipment->tracking_number }}</span>
                        <div style="font-size: 0.65rem; color: var(--text-gray); margin-top: 0.4rem; font-weight: 700;">
                            CREATED: {{ $shipment->created_at->format('d M, H:i') }}
                        </div>
                    </td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">{{ substr($shipment->user ? $shipment->user->name : 'G', 0, 1) }}</div>
                            <div>
                                <div style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem;">{{ $shipment->user ? $shipment->user->name : 'Guest Client' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $shipment->user ? $shipment->user->email : 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem;">{{ $shipment->origin }}</div>
                        <div style="font-size: 0.75rem; color: var(--primary-green); font-weight: 700;">↓</div>
                        <div style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem;">{{ $shipment->destination }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-dark);">{{ $shipment->service ?? 'General Cargo' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $shipment->border_status ? 'Border: ' . $shipment->border_status : 'No border update' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 1rem; font-weight: 900; color: var(--text-dark);">
                            {{ number_format($shipment->cost ?? 0, 2) }} ZMW
                        </div>
                        <div style="font-size: 0.7rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">
                            ETA: {{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('M d') : 'TBD' }}
                        </div>
                    </td>
                    <td>
                        @php
                            $statusClass = match(strtolower($shipment->status)) {
                                'pending' => 'status-pending',
                                'delivered' => 'status-delivered',
                                default => 'status-transit'
                            };
                            $icon = match(strtolower($shipment->status)) {
                                'pending' => 'fa-clock',
                                'delivered' => 'fa-check-double',
                                default => 'fa-truck-fast'
                            };
                        @endphp
                        <div class="status-pill {{ $statusClass }}">
                            <i class="fas {{ $icon }}"></i> {{ $shipment->status }}
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('admin.shipments.edit', $shipment) }}" class="action-btn" title="Edit Shipment"><i class="fas fa-pen-to-square"></i></a>
                            <a href="{{ route('admin.shipments.edit', $shipment) }}#add-event" class="action-btn" title="Add Tracking Event"><i class="fas fa-location-dot"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 5rem 0; color: var(--text-gray);">
                        <i class="fas fa-box-open" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                        <h3 style="font-weight: 800;">No Shipments Found</h3>
                        <p style="font-size: 0.9rem;">There are no active or historical shipments in the system.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
