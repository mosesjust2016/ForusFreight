@extends('layouts.dashboard')

@section('title', 'Invoices & Payments - Forus Freight')

@section('styles')
<style>
    .invoice-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-table th {
        text-align: left;
        padding: 1.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f8fafc;
    }

    .invoice-table td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-paid { background: #f0fdf4; color: #15803d; }
    .status-overdue { background: #fef2f2; color: #b91c1c; }

    .action-btn {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-pay {
        background: var(--primary-green);
        color: white;
    }

    .btn-pay:hover {
        background: #3d8b40;
        transform: translateY(-2px);
    }

    .btn-view {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-view:hover {
        background: white;
        border-color: var(--primary-green);
        color: var(--primary-green);
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Invoices & Payments</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Manage your billing, download receipts, and settle outstanding balances.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div style="background: white; padding: 1rem 1.5rem; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid #f1f5f9; text-align: right;">
                <p style="font-size: 0.65rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.25rem;">Outstanding Balance</p>
                <h3 style="font-size: 1.25rem; font-weight: 900; color: #ef4444;">{{ number_format($invoices->where('status', 'pending')->sum('amount'), 2) }} ZMW</h3>
            </div>
        </div>
    </div>
</div>

<div class="invoice-card">
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Shipment</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td>INV-{{ $invoice->invoice_number }}</td>
                <td>
                    @if($invoice->shipment)
                        <span style="color: var(--primary-green); font-weight: 800;">#{{ $invoice->shipment->tracking_number }}</span>
                    @else
                        <span style="color: #cbd5e1;">N/A</span>
                    @endif
                </td>
                <td style="font-weight: 800;">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                <td>
                    <span class="status-badge status-{{ strtolower($invoice->status) }}">
                        {{ $invoice->status }}
                    </span>
                </td>
                <td style="text-align: right; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('client.invoices.show', $invoice->invoice_number) }}" class="action-btn btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    @if($invoice->status === 'pending')
                    <a href="{{ route('client.payments.checkout', $invoice->invoice_number) }}" class="action-btn btn-pay">
                        <i class="fas fa-credit-card"></i> Pay Now
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-receipt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.1;"></i>
                    <p>No invoices found in your account.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
