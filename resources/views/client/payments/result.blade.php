@extends('layouts.dashboard')

@section('title', 'Payment status - Forus Freight')

@section('styles')
<style>
    .pr {
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

        max-width: 640px;
    }

    .pr-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 2.5rem 1.75rem;
        margin-top: 1.25rem;
        text-align: center;
    }

    .pr-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.25rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        background: #fff7ed;
        color: var(--orange);
    }

    .pr-icon.is-success { background: #dcfce7; color: #166534; }
    .pr-icon.is-failed { background: #fee2e2; color: #b91c1c; }

    .pr-icon.is-pending i { animation: prspin 1.4s linear infinite; }

    @keyframes prspin { to { transform: rotate(360deg); } }

    .pr-card h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
    }

    .pr-card p {
        color: var(--ink-2);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 44ch;
        margin: 0 auto;
    }

    .pr-amount {
        margin-top: 1rem;
        font-weight: 800;
        font-size: 1.35rem;
    }

    .pr-ref {
        font-family: var(--mono);
        font-size: 0.85rem;
        color: var(--muted);
    }

    .pr-error {
        margin-top: 1.1rem;
        padding: 0.8rem 1rem;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 0.87rem;
        font-weight: 600;
        text-align: left;
    }

    .pr-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .pr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 46px;
        padding: 0 1.35rem;
        border-radius: 12px;
        border: 1.5px solid transparent;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
    }

    .pr-btn.is-primary { background: var(--orange); color: var(--ink); }
    .pr-btn.is-primary:hover { background: #ff7a26; }

    .pr-btn.is-outline { background: #fff; border-color: var(--line-strong); color: var(--ink); }
    .pr-btn.is-outline:hover { border-color: var(--ink); }

    .pr-note {
        margin-top: 1.35rem;
        font-size: 0.8rem;
        color: var(--muted);
    }
</style>
@endsection

@section('content')
@php
    $initialState = $payment->status; // pending | successful | failed
    $isMomo = $payment->method === 'momo';
@endphp

<div class="pr">
    <section class="pc-card pr-card" id="prCard" data-state="{{ $initialState }}"
             data-status-url="{{ route('client.payments.status', $payment) }}">
        <div class="pr-icon {{ $initialState === 'successful' ? 'is-success' : ($initialState === 'failed' ? 'is-failed' : 'is-pending') }}" id="prIcon">
            <i class="fas {{ $initialState === 'successful' ? 'fa-check' : ($initialState === 'failed' ? 'fa-xmark' : 'fa-spinner') }}" id="prIconGlyph" aria-hidden="true"></i>
        </div>

        <h1 id="prTitle">
            @if($initialState === 'successful')
                Payment received
            @elseif($initialState === 'failed')
                Payment not completed
            @elseif($isMomo)
                Check your phone
            @else
                Confirming your payment
            @endif
        </h1>

        <p id="prCopy">
            @if($initialState === 'successful')
                Thank you — your invoice is marked as paid.
            @elseif($initialState === 'failed')
                The payment didn’t go through. You can try again or ask us for payment details.
            @elseif($isMomo)
                We’ve sent a payment prompt to <strong>{{ $payment->account_number ?? 'your phone' }}</strong> — enter your PIN to approve it. This page updates on its own.
            @else
                This only takes a moment. This page updates on its own.
            @endif
        </p>

        <div class="pr-amount">{{ money($payment->amount, $payment->currency) }} <span class="pr-ref">· {{ $payment->invoice->invoice_number ?? '' }}</span></div>

        @if(session('error'))
            <div class="pr-error" role="alert">{{ session('error') }}</div>
        @endif

        <div class="pr-actions" id="prActions">
            @if($initialState === 'successful')
                <a class="pr-btn is-primary" href="{{ $payment->invoice ? route('client.invoices.show', $payment->invoice) : route('client.profile') }}">View invoice</a>
                <a class="pr-btn is-outline" href="{{ route('client.profile') }}#invoices">Back to account</a>
            @elseif($initialState === 'failed')
                @if($payment->invoice && $payment->invoice->canPayOnline())
                    <a class="pr-btn is-primary" href="{{ route('client.payments.checkout', $payment->invoice) }}">Try again</a>
                @endif
                <a class="pr-btn is-outline" href="{{ route('client.profile') }}#invoices">Back to account</a>
            @else
                <a class="pr-btn is-outline" href="{{ route('client.profile') }}#invoices">Back to account</a>
            @endif
        </div>

        <p class="pr-note" id="prNote" {{ $initialState === 'pending' ? '' : 'hidden' }}>
            Still waiting? We’ll mark the invoice paid as soon as the payment confirms — you can close this page.
        </p>
    </section>
</div>

<script>
    (function () {
        const card = document.getElementById('prCard');
        if (card.dataset.state !== 'pending') return;

        const statusUrl = card.dataset.statusUrl;
        const icon = document.getElementById('prIcon');
        const glyph = document.getElementById('prIconGlyph');
        const title = document.getElementById('prTitle');
        const copy = document.getElementById('prCopy');
        const actions = document.getElementById('prActions');
        const note = document.getElementById('prNote');

        let attempts = 0;
        const MAX_ATTEMPTS = 45; // ~3 minutes at 4s intervals

        function showState(state) {
            card.dataset.state = state;
            icon.classList.remove('is-pending', 'is-success', 'is-failed');

            if (state === 'successful') {
                icon.classList.add('is-success');
                glyph.className = 'fas fa-check';
                title.textContent = 'Payment received';
                copy.textContent = 'Thank you — your invoice is marked as paid.';
                note.hidden = true;
                actions.innerHTML =
                    '<a class="pr-btn is-primary" href="{{ $payment->invoice ? route('client.invoices.show', $payment->invoice) : route('client.profile') }}">View invoice</a>' +
                    '<a class="pr-btn is-outline" href="{{ route('client.profile') }}#invoices">Back to account</a>';
            } else if (state === 'failed') {
                icon.classList.add('is-failed');
                glyph.className = 'fas fa-xmark';
                title.textContent = 'Payment not completed';
                copy.textContent = 'The payment didn’t go through. You can try again or ask us for payment details.';
                note.hidden = true;
                actions.innerHTML =
                    @if($payment->invoice && $payment->invoice->canPayOnline())
                        '<a class="pr-btn is-primary" href="{{ route('client.payments.checkout', $payment->invoice) }}">Try again</a>' +
                    @endif
                    '<a class="pr-btn is-outline" href="{{ route('client.profile') }}#invoices">Back to account</a>';
            }
        }

        async function poll() {
            if (attempts++ >= MAX_ATTEMPTS) {
                note.hidden = false;
                return;
            }

            try {
                const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                if (response.ok) {
                    const data = await response.json();
                    if (data.status === 'successful' || data.status === 'failed') {
                        showState(data.status);
                        return;
                    }
                }
            } catch (e) {
                // Transient network problem — keep polling.
            }

            setTimeout(poll, 4000);
        }

        setTimeout(poll, 4000);
    })();
</script>
@endsection
