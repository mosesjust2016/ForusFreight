@extends('layouts.dashboard')

@section('title', 'Invoices & Payments - Forus Freight')

@section('styles')
<style>
    .invoice-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        overflow-x: auto;
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
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f8fafc;
    }

    .invoice-table td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-paid { background: #f0fdf4; color: #15803d; }
    .status-overdue { background: #fef2f2; color: #b91c1c; }

    .action-btn {
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }

    .btn-pay {
        background: #ff6200;
        color: white;
        box-shadow: 0 4px 10px rgba(255, 98, 0, 0.2);
    }

    .btn-pay:hover {
        background: #e65800;
        transform: translateY(-2px);
    }

    .btn-view {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-view:hover {
        background: white;
        border-color: #007f7f;
        color: #007f7f;
    }

    .balance-card {
        background: white;
        padding: 1.5rem 2rem;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Billing & Invoices</h1>
        <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Manage your payments and download historical invoices.</p>
    </div>
    <div class="balance-card">
        <p style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.25rem;">Total Outstanding</p>
        <h3 style="font-size: 1.75rem; font-weight: 900; color: #ef4444;">ZMW {{ number_format($invoices->where('status', 'Pending')->sum('amount'), 2) }}</h3>
    </div>
</div>

<div class="invoice-card">
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Shipment Ref</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #64748b;">INV-{{ $invoice->invoice_number }}</span>
                </td>
                <td>
                    @if($invoice->shipment)
                        <a href="{{ route('tracking.show', $invoice->shipment->serial_no) }}" style="color: #007f7f; text-decoration: none; font-weight: 700;">{{ $invoice->shipment->serial_no }}</a>
                    @else
                        <span style="color: #cbd5e1;">N/A</span>
                    @endif
                </td>
                <td style="font-weight: 800; font-size: 1rem;">ZMW {{ number_format($invoice->amount, 2) }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: {{ $invoice->due_date->isPast() && $invoice->status == 'Pending' ? '#ef4444' : '#64748b' }}">
                        <i class="far fa-calendar-alt"></i>
                        {{ $invoice->due_date->format('M d, Y') }}
                    </div>
                </td>
                <td>
                    <span class="status-badge status-{{ strtolower($invoice->status) }}">
                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                        {{ $invoice->status }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <a href="#" class="action-btn btn-view">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        @if($invoice->status === 'Pending')
                        <a href="#" class="action-btn btn-pay">
                            <i class="fas fa-credit-card"></i> Pay Now
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 5rem 0;">
                    <img src="https://illustrations.popsy.co/emerald/clumsy-person-dropping-papers.svg" alt="No invoices" style="width: 200px; margin-bottom: 2rem; opacity: 0.5;">
                    <p style="color: #64748b; font-weight: 600;">No invoices found in your account.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
