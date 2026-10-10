@extends('layouts.dashboard')

@section('title', 'Pay invoice ' . $invoice->invoice_number . ' - Forus Freight')

@section('styles')
<style>
    .pc {
        --ink: #0d1b20;
        --ink-2: #33444a;
        --muted: #56666c;
        --teal: #007f7f;
        --teal-deep: #005e5e;
        --orange: #ff6200;
        --line: rgba(13, 27, 32, 0.12);
        --line-strong: rgba(13, 27, 32, 0.22);
        --display: 'Archivo', 'Inter', sans-serif;
        --mono: 'IBM Plex Mono', ui-monospace, monospace;

        max-width: 720px;
    }

    .pc-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 40px;
        margin-bottom: 0.75rem;
        color: var(--ink-2);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .pc-back:hover { color: var(--ink); }

    .pc h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        letter-spacing: -0.02em;
    }

    .pc-sub { margin-top: 0.35rem; color: var(--ink-2); }

    .pc-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 1.5rem;
        margin-top: 1.25rem;
    }

    .pc-card h2 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .pc-summary {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .pc-amount {
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.01em;
    }

    .pc-meta {
        font-size: 0.875rem;
        color: var(--muted);
        line-height: 1.6;
        text-align: right;
    }

    .pc-meta a { color: var(--teal-deep); font-weight: 600; text-decoration: none; }
    .pc-meta a:hover { text-decoration: underline; }
    .pc-ref { font-family: var(--mono); }

    .pc-method + .pc-method {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--line);
    }

    .pc-method-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }

    .pc-method-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        background: #f1f5f9;
        color: var(--ink);
    }

    .pc-method-icon.is-momo { background: #fff7ed; color: #c2410c; }
    .pc-method-icon.is-card { background: #eef2ff; color: #3730a3; }

    .pc-method-head h3 { font-size: 0.98rem; font-weight: 700; }
    .pc-method-head p { font-size: 0.82rem; color: var(--muted); }

    .pc-field { display: flex; flex-direction: column; gap: 0.45rem; }

    .pc-field label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pc-in {
        width: 100%;
        min-height: 46px;
        padding: 0.7rem 1rem;
        border: 1.5px solid var(--line-strong);
        border-radius: 12px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .pc-in:focus { outline: none; border-color: var(--teal); }

    .pc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        min-height: 48px;
        padding: 0 1.4rem;
        border: none;
        border-radius: 12px;
        background: var(--orange);
        color: var(--ink);
        font: inherit;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }

    .pc-btn:hover { background: #ff7a26; }

    .pc-btn.is-outline {
        background: #fff;
        border: 1.5px solid var(--line-strong);
        color: var(--ink);
    }

    .pc-btn.is-outline:hover { border-color: var(--ink); background: #fff; }

    .pc-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.1rem;
    }

    .pc-hint {
        font-size: 0.8rem;
        color: var(--muted);
        line-height: 1.55;
        margin-top: 0.6rem;
    }

    .pc-bank {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .pc-bank p { font-size: 0.9rem; color: var(--ink-2); line-height: 1.55; }

    @media (max-width: 640px) {
        .pc-card { padding: 1.25rem 1.1rem; }
        .pc-meta { text-align: left; }
        .pc-btn { width: 100%; }
        .pc-actions { flex-direction: column; }
    }
</style>
@endsection

@section('content')
@php
    $due = $invoice->due_date ? $invoice->due_date->format('j M Y') : 'on receipt';
@endphp

<div class="pc">
    <a href="{{ route('client.profile') }}#invoices" class="pc-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Invoices</a>

    <h1>Pay invoice</h1>
    <p class="pc-sub">Choose how you’d like to pay {{ money($invoice->amount, $invoice->currency) }}.</p>

    <section class="pc-card" aria-label="Invoice summary">
        <div class="pc-summary">
            <span class="pc-amount">{{ money($invoice->amount, $invoice->currency) }}</span>
            <span class="pc-meta">
                <span class="pc-ref">{{ $invoice->invoice_number }}</span><br>
                @if($invoice->shipment)
                    Shipment <a href="{{ route('tracking.show', $invoice->shipment->serial_no) }}">{{ $invoice->shipment->serial_no }}</a><br>
                @endif
                Due {{ $due }}
            </span>
        </div>
    </section>

    <section class="pc-card" aria-label="Payment methods">
        <div class="pc-method">
            <div class="pc-method-head">
                <span class="pc-method-icon is-momo" aria-hidden="true"><i class="fas fa-mobile-screen-button"></i></span>
                <span>
                    <h3>Mobile money</h3>
                    <p>MTN, Airtel or Zamtel — approve the prompt on your phone.</p>
                </span>
            </div>
            <form action="{{ route('client.payments.store', $invoice) }}" method="POST">
                @csrf
                <input type="hidden" name="method" value="momo">
                <div class="pc-field">
                    <label for="pc_phone">Phone number to charge</label>
                    <input id="pc_phone" type="tel" name="phone" class="pc-in" value="{{ old('phone', Auth::user()->phone ?? '') }}" placeholder="e.g. 260970026344" required>
                    @error('phone')<span style="color:#b91c1c;font-size:0.78rem;font-weight:700;">{{ $message }}</span>@enderror
                </div>
                <div class="pc-actions">
                    <button type="submit" class="pc-btn" data-loading-label="Sending prompt…">
                        Send payment prompt <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="pc-hint">You’ll get a prompt on this number — enter your PIN to approve. If it doesn’t arrive, dial <strong>*115#</strong> (MTN).</p>
            </form>
        </div>

        <div class="pc-method">
            <div class="pc-method-head">
                <span class="pc-method-icon is-card" aria-hidden="true"><i class="fas fa-credit-card"></i></span>
                <span>
                    <h3>Debit or credit card</h3>
                    <p>Visa, Mastercard or American Express.</p>
                </span>
            </div>
            <form action="{{ route('client.payments.store', $invoice) }}" method="POST">
                @csrf
                <input type="hidden" name="method" value="card">
                <div class="pc-actions">
                    <button type="submit" class="pc-btn is-outline" data-loading-label="Opening secure page…">
                        Pay by card <i class="fas fa-lock" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="pc-hint">You’ll be taken to our payment provider’s secure page to enter your card details.</p>
            </form>
        </div>
    </section>

    <section class="pc-card" aria-label="Bank transfer">
        <div class="pc-bank">
            <p><strong>Prefer bank transfer?</strong><br>Ask for our details and send proof of payment — we confirm it and mark the invoice paid.</p>
            <a class="pc-btn is-outline" href="https://wa.me/260572788685?text={{ urlencode('Hi Forus Freight, I would like to pay invoice ' . $invoice->invoice_number . ' by bank transfer. Please send me the payment details.') }}" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp" aria-hidden="true"></i> How to pay
            </a>
        </div>
    </section>
</div>
@endsection
