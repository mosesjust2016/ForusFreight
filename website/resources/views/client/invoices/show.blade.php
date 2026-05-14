@extends('layouts.dashboard')

@section('title', 'Invoice Details - Forus Freight')

@section('styles')
<style>
    .invoice-detail-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .invoice-paper {
        background: white;
        border-radius: 30px;
        padding: 5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }

    .invoice-paper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background: var(--primary-green);
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4rem;
    }

    .company-info h2 {
        font-size: 1.5rem;
        font-weight: 900;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .company-info p {
        font-size: 0.85rem;
        color: var(--text-gray);
        line-height: 1.5;
    }

    .invoice-meta {
        text-align: right;
    }

    .invoice-meta h1 {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 4rem;
    }

    .meta-box h4 {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        letter-spacing: 0.05em;
    }

    .meta-box p {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .line-items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3rem;
    }

    .line-items th {
        text-align: left;
        padding: 1.25rem 0;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        border-bottom: 2px solid #f8fafc;
    }

    .line-items td {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .totals-area {
        display: flex;
        justify-content: flex-end;
    }

    .totals-box {
        width: 300px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-gray);
    }

    .total-row.grand-total {
        border-top: 2px solid var(--text-dark);
        margin-top: 1rem;
        padding-top: 1.5rem;
        color: var(--text-dark);
        font-size: 1.25rem;
        font-weight: 900;
    }

    .btn-action {
        padding: 1rem 2rem;
        border-radius: 15px;
        font-weight: 800;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s;
    }

    @media print {
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .sidebar, .top-bar, .btn-action, .welcome-section { display: none !important; }
        .invoice-paper { box-shadow: none !important; border: none !important; padding: 0 !important; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800;">Invoice Details</h1>
            <p>Review the breakdown of your logistics service fees.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="javascript:window.print()" class="btn-action" style="background: #f1f5f9; color: #475569;">
                <i class="fas fa-print"></i> Print Invoice
            </a>
            @if($invoice->status === 'pending')
            <a href="{{ route('client.payments.checkout', $invoice->invoice_number) }}" class="btn-action" style="background: var(--primary-green); color: white;">
                <i class="fas fa-credit-card"></i> Pay Now
            </a>
            @endif
        </div>
    </div>
</div>

<div class="invoice-detail-container">
    <div class="invoice-paper">
        <div class="invoice-header">
            <div class="company-info">
                <h2>Forus Freight Limited</h2>
                <p>Plot 123, Great East Road<br>
                Lusaka, Zambia<br>
                Phone: +260 96 123 4567<br>
                Email: accounts@forusfl.co.zm</p>
            </div>
            <div class="invoice-meta">
                <h4>INVOICE</h4>
                <h1>#{{ $invoice->invoice_number }}</h1>
                <span style="background: {{ $invoice->status === 'paid' ? '#f0fdf4' : '#fff7ed' }}; color: {{ $invoice->status === 'paid' ? '#15803d' : '#c2410c' }}; padding: 0.5rem 1rem; border-radius: 50px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase;">
                    {{ $invoice->status }}
                </span>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-box">
                <h4>Billed To</h4>
                <p>{{ Auth::user()->name }}</p>
                <p style="font-weight: 500; font-size: 0.85rem; color: var(--text-gray);">{{ Auth::user()->email }}</p>
            </div>
            <div class="meta-box" style="text-align: right;">
                <h4>Date Details</h4>
                <p>Issued: {{ $invoice->created_at->format('M d, Y') }}</p>
                <p style="color: #ef4444;">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
            </div>
        </div>

        <table class="line-items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Rate</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->shipment)
                <tr>
                    <td>
                        <div style="font-weight: 800; margin-bottom: 0.25rem;">Freight Services - #{{ $invoice->shipment->tracking_number }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray);">Route: {{ $invoice->shipment->origin }} to {{ $invoice->shipment->destination }}</div>
                    </td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2) }}</td>
                </tr>
                @else
                <tr>
                    <td>General Logistics Consulting / Handling</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($invoice->amount, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals-area">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>VAT (16%)</span>
                    <span>Included</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total Amount</span>
                    <span>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
                </div>
            </div>
        </div>

        <div style="margin-top: 5rem; padding-top: 2rem; border-top: 1px solid #f8fafc; color: var(--text-gray); font-size: 0.75rem; line-height: 1.6;">
            <h4 style="color: var(--text-dark); margin-bottom: 0.5rem; font-weight: 800;">Payment Instructions</h4>
            <p>Please use <strong>INV-{{ $invoice->invoice_number }}</strong> as your payment reference. For Bank Transfers, send proof of payment to accounts@forusfreight.co.zm. Payments should be made within 14 days of the invoice date.</p>
        </div>
    </div>
</div>
@endsection
