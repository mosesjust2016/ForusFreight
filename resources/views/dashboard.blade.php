@extends('layouts.dashboard')

@section('title', 'Home - Forus Freight')

@php
    $user = Auth::user();
    $firstName = explode(' ', trim($user->name))[0] ?: 'there';
    $addr = config('forus.forwarding_address');
    $mark = config('forus.parcel_code_prefix', 'ZMFFL');
    // What the customer writes as the recipient: shipping mark + their name + phone
    $recipientLine = trim($mark . ' ' . $user->name . ' ' . ($user->phone ?? ''));
    $addressText = implode("\n", array_filter([
        $recipientLine,
        $addr['name'] ?? null,
        $addr['line1'] ?? null,
        $addr['line2'] ?? null,
        $addr['line3'] ?? null,
        $addr['country'] ?? null,
        isset($addr['phone']) ? 'Tel: ' . $addr['phone'] : null,
    ]));
    $whatsapp = '260572788685';
@endphp

@section('styles')
<style>
    .h-greet h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        letter-spacing: -0.02em;
        line-height: 1.15;
    }

    .h-greet p {
        margin-top: 0.35rem;
        color: var(--ink-2);
    }

    .h-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
        gap: 1.25rem;
        margin-top: 1.75rem;
        align-items: start;
    }

    .h-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 1.5rem;
    }

    .h-card-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .h-card-head h2 {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .h-card-head a {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--teal-deep);
        text-decoration: none;
    }

    .h-card-head a:hover { text-decoration: underline; }

    /* China address */
    .h-address {
        background: var(--ink);
        color: #fff;
        border-color: var(--ink);
        grid-row: span 2;
    }

    .h-address .h-card-head h2 { color: #fff; }

    .h-address-box {
        position: relative;
        margin-top: 0.25rem;
        padding: 1.1rem 1.15rem;
        background: #fffdf7;
        color: var(--ink);
        border-radius: 12px;
        font-family: var(--mono);
        font-size: 0.86rem;
        line-height: 1.6;
        white-space: pre-line;
        word-break: break-word;
    }

    .h-address-box strong {
        color: #b54400;
        font-weight: 500;
    }

    .h-copy {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 40px;
        padding: 0 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        background: transparent;
        color: #fff;
        font: inherit;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .h-copy:hover { border-color: #fff; }
    .h-copy.is-done { background: #0f766e; border-color: #0f766e; }

    .h-copy-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.85rem;
    }

    .h-address-note {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #b5c4c9;
        line-height: 1.5;
    }

    .h-address-note a { color: #5eead4; font-weight: 600; }

    /* Shipments */
    .h-list { list-style: none; }

    .h-ship {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.35rem 1rem;
        align-items: center;
        padding: 0.9rem 0;
        border-top: 1px solid var(--line);
        color: inherit;
        text-decoration: none;
    }

    .h-list li:first-child .h-ship { border-top: none; padding-top: 0; }

    .h-ship:hover .h-ship-ref { color: var(--teal-deep); }

    .h-ship-ref {
        font-family: var(--mono);
        font-weight: 500;
        font-size: 0.92rem;
    }

    .h-ship-desc {
        font-size: 0.85rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .h-status {
        justify-self: end;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: #e6f6f4;
        color: #005e5e;
        white-space: nowrap;
    }

    .h-status.is-done { background: #dcfce7; color: #166534; }
    .h-status.is-alert { background: #fef3c7; color: #92400e; }

    .h-bar {
        grid-column: 1 / -1;
        height: 5px;
        border-radius: 5px;
        background: var(--paper-2);
        overflow: hidden;
    }

    .h-bar span {
        display: block;
        height: 100%;
        border-radius: 5px;
        background: linear-gradient(90deg, var(--teal), var(--teal-bright));
    }

    .h-empty {
        color: var(--ink-2);
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .h-empty a { color: var(--teal-deep); font-weight: 700; }

    /* To pay */
    .h-due {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.2rem 1rem;
        align-items: center;
        padding: 0.85rem 0;
        border-top: 1px solid var(--line);
    }

    .h-list li:first-child .h-due { border-top: none; padding-top: 0; }

    .h-due-amount {
        font-weight: 800;
        font-size: 1.1rem;
        text-align: right;
    }

    .h-due-meta {
        font-size: 0.82rem;
        color: var(--muted);
    }

    .h-due-ref {
        font-family: var(--mono);
        font-size: 0.88rem;
    }

    .h-pay {
        grid-column: 1 / -1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 40px;
        margin-top: 0.5rem;
        border-radius: 10px;
        background: var(--paper);
        border: 1px solid var(--line-strong);
        color: var(--ink);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
    }

    .h-pay:hover { border-color: var(--ink); }

    .h-allclear {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: #166534;
        font-weight: 600;
        font-size: 0.92rem;
    }

    /* Tracking lookup result (from the old ?serial_no= links) */
    .h-track-result { margin-top: 1.5rem; }

    @media (max-width: 960px) {
        .h-grid { grid-template-columns: 1fr; }
        .h-address { grid-row: auto; }
    }
</style>
@endsection

@section('content')
    <div class="h-greet">
        <h1>Hi {{ $firstName }}</h1>
        <p>
            @if($stats['active_shipments'] > 0)
                You have {{ $stats['active_shipments'] }} {{ \Illuminate\Support\Str::plural('shipment', $stats['active_shipments']) }} on the way.
            @else
                Nothing on the way right now.
            @endif
        </p>
    </div>

    @if($trackedShipment || $trackingError)
        <div class="h-card h-track-result">
            @if($trackingError)
                <p class="h-empty">{{ $trackingError }}</p>
            @else
                <div class="h-card-head">
                    <h2>{{ $trackedShipment->serial_no }}</h2>
                    <a href="{{ route('tracking.show', $trackedShipment->serial_no) }}">Full details</a>
                </div>
                <p class="h-empty">{{ $trackedShipment->status_label }} · {{ $trackedShipment->origin }} → {{ $trackedShipment->destination }}</p>
            @endif
        </div>
    @endif

    <div class="h-grid">
        {{-- Your China address: the thing China shoppers need most often --}}
        <section class="h-card h-address" aria-labelledby="addrTitle">
            <div class="h-card-head">
                <h2 id="addrTitle">Your China address</h2>
            </div>
            <div class="h-address-box" id="chinaAddress"><strong>{{ $recipientLine }}</strong>
{{ $addr['name'] ?? '' }}
{{ $addr['line1'] ?? '' }}
{{ $addr['line2'] ?? '' }}
{{ $addr['line3'] ?? '' }}
{{ $addr['country'] ?? '' }}
@if(!empty($addr['phone']))Tel: {{ $addr['phone'] }}@endif</div>
            <div class="h-copy-row">
                <button type="button" class="h-copy" data-copy="{{ $addressText }}"><i class="far fa-copy" aria-hidden="true"></i> <span>Copy address</span></button>
                @if(!empty($addr['address_cn']))
                    <button type="button" class="h-copy" data-copy="{{ $recipientLine . "\n" . $addr['address_cn'] . (isset($addr['phone']) ? "\n" . $addr['phone'] : '') }}"><i class="far fa-copy" aria-hidden="true"></i> <span>Copy in Chinese</span></button>
                @endif
            </div>
            <p class="h-address-note">Always include <strong>{{ $mark }}</strong> with your name and phone so we can match parcels to you. <a href="{{ route('client.getting-started') }}">How it works</a></p>
        </section>

        {{-- Shipments --}}
        <section class="h-card" aria-labelledby="shipTitle">
            <div class="h-card-head">
                <h2 id="shipTitle">Your shipments</h2>
                @if($stats['total_shipments'] > 0)
                    <a href="{{ route('client.shipments') }}">See all ({{ $stats['total_shipments'] }})</a>
                @endif
            </div>
            @if($recentShipments->isEmpty())
                <p class="h-empty">No shipments yet. When your parcels arrive at our China warehouse they’ll appear here — or <a href="{{ route('client.shipments.create') }}">tell us what’s coming</a>.</p>
            @else
                <ul class="h-list">
                    @foreach($recentShipments as $s)
                        @php
                            $canon = \App\Models\Shipment::canonicalStatus($s->status);
                            $tone = $canon === 'DELIVERED' ? 'is-done' : (in_array($canon, ['ON_HOLD', 'EXCEPTION'], true) ? 'is-alert' : '');
                            $pct = max(0, min(100, (int) $s->delivery_progress_percent));
                        @endphp
                        <li>
                            <a class="h-ship" href="{{ route('tracking.show', $s->serial_no) }}">
                                <span style="min-width: 0;">
                                    <span class="h-ship-ref">{{ $s->serial_no }}</span>
                                    <span class="h-ship-desc" style="display: block;">{{ $s->description ?: ($s->origin . ' → ' . $s->destination) }}</span>
                                </span>
                                <span class="h-status {{ $tone }}">{{ $s->status_label }}</span>
                                <span class="h-bar" aria-hidden="true"><span style="width: {{ $pct }}%;"></span></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- To pay --}}
        <section class="h-card" aria-labelledby="payTitle">
            <div class="h-card-head">
                <h2 id="payTitle">To pay</h2>
                <a href="{{ route('client.invoices') }}">All invoices</a>
            </div>
            @if($pendingPayments->isEmpty())
                <p class="h-allclear"><i class="fas fa-circle-check" aria-hidden="true"></i> You’re all paid up.</p>
            @else
                <ul class="h-list">
                    @foreach($pendingPayments as $inv)
                        <li>
                            <div class="h-due">
                                <span>
                                    <span class="h-due-ref">{{ $inv->invoice_number }}</span>
                                    <span class="h-due-meta" style="display: block;">
                                        @if($inv->shipment) {{ $inv->shipment->serial_no }} · @endif
                                        Due {{ $inv->due_date ? $inv->due_date->format('j M Y') : 'on receipt' }}
                                    </span>
                                </span>
                                <span class="h-due-amount">{{ money($inv->amount, $inv->currency) }}</span>
                                @if($inv->canPayOnline())
                                    <a class="h-pay" href="{{ route('client.payments.checkout', $inv) }}">
                                        <i class="fas fa-credit-card" aria-hidden="true"></i> Pay
                                    </a>
                                @else
                                    <a class="h-pay" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hi Forus Freight, I would like to pay invoice ' . $inv->invoice_number . '. Please send payment details.') }}" target="_blank" rel="noopener">
                                        <i class="fab fa-whatsapp" aria-hidden="true"></i> How to pay
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="h-due-meta" style="margin-top: 0.85rem;">We accept bank transfer and mobile money.</p>
            @endif
        </section>
    </div>

    <script>
        // Copy the address to the clipboard, with a short "Copied" confirmation
        document.querySelectorAll('.h-copy').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                const label = btn.querySelector('span');
                const original = label.textContent;
                try {
                    await navigator.clipboard.writeText(btn.dataset.copy);
                    label.textContent = 'Copied';
                } catch (e) {
                    label.textContent = 'Press and hold to copy';
                }
                btn.classList.add('is-done');
                setTimeout(function () { label.textContent = original; btn.classList.remove('is-done'); }, 2000);
            });
        });
    </script>
@endsection
