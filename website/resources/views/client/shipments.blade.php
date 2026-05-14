@extends('layouts.dashboard')

@section('title', 'My Shipments - Forus Freight')

@section('styles')
<style>
    .shipment-table-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 1rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-gray);
        border-bottom: 2px solid #f1f5f9;
    }

    td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-pill {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <h1>Cargo Management</h1>
    <p>View and manage all your freight requests.</p>
</div>

@if(session('success'))
    <div style="background: #e8f5e9; border-left: 4px solid var(--primary-green); padding: 1rem; border-radius: 12px; margin-bottom: 2rem; color: #2e7d32; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="shipment-table-card">
    <table>
        <thead>
            <tr>
                <th>Tracking ID</th>
                <th>Origin / Destination</th>
                <th>Status</th>
                <th>ETA</th>
                <th>Cost (ZMK)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
            <tr>
                <td>#{{ $shipment->tracking_number }}</td>
                <td>
                    <div style="font-size: 0.85rem;">{{ $shipment->origin }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-gray);">to {{ $shipment->destination }}</div>
                </td>
                <td>
                    <span class="status-pill" style="background: #e3f2fd; color: #1565c0;">{{ $shipment->status }}</span>
                </td>
                <td>{{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('d M') : 'TBD' }}</td>
                <td>{{ number_format($shipment->cost, 2) }}</td>
                <td>
                    <a href="{{ route('tracking.show', $shipment->tracking_number) }}" style="color: var(--primary-green);"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
