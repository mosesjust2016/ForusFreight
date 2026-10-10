@extends('layouts.app')

@section('title', 'Track Your Shipment - Forus Freight')
@section('meta_description', 'Track your Forus Freight shipment in real time by entering your tracking number.')

@php
    $footerSections = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
    $whatsappNumber = $footerSections['whatsapp_number'] ?? '260572788685';
    $hasShipment = isset($shipment) && $shipment;

    if ($hasShipment) {
        $trackingRef = $shipment->tracking_number ?: $shipment->serial_no;
        $whatsappLink = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode('Hi Forus Freight, I have a question about shipment ' . $trackingRef);
    } else {
        $whatsappLink = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode('Hi Forus Freight, I need help tracking my shipment');
    }
@endphp

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        /* ---------- Search ---------- */
        .t-hero {
            padding: 3.5rem 0 3rem;
        }

        .t-hero-inner {
            max-width: 860px;
        }

        .t-hero h1 {
            font-size: clamp(2.2rem, 4.2vw, 3.4rem);
            margin: 1rem 0 0.9rem;
        }

        .t-hero .h-lead {
            margin-bottom: 2rem;
        }

        .t-search {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: 0 24px 50px rgba(13, 27, 32, 0.08);
        }

        .t-search label {
            display: block;
            font-family: var(--mono);
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.65rem;
        }

        .t-search-row {
            display: flex;
            gap: 0.75rem;
        }

        .t-search input {
            flex: 1;
            min-width: 0;
            height: 60px;
            padding: 0 1.1rem;
            border: 2px solid var(--line-strong);
            border-radius: 14px;
            background: var(--paper);
            font-family: var(--mono);
            font-size: 1.15rem;
            letter-spacing: 0.02em;
            color: var(--ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .t-search input::placeholder {
            color: #8a979c;
        }

        .t-search input:focus {
            outline: none;
            background: #fff;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.15);
        }

        .t-search input[aria-invalid="true"] {
            border-color: #dc2626;
        }

        .t-search .h-btn {
            min-height: 60px;
            padding: 0 1.75rem;
            font-size: 1.05rem;
        }

        .t-hint {
            margin-top: 0.85rem;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .t-hint code {
            font-family: var(--mono);
            font-size: 0.85rem;
            color: var(--ink-2);
            background: var(--paper-2);
            padding: 0.1rem 0.4rem;
            border-radius: 5px;
        }

        .t-alert {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 1rem 1.15rem;
            border-radius: 14px;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .t-alert--error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .t-alert--info {
            background: #ecfdf9;
            border: 1px solid #99f6e4;
            color: #115e59;
        }

        .t-alert i {
            margin-top: 0.2rem;
        }

        .t-field-error {
            color: #b91c1c;
            font-size: 0.9rem;
            margin-top: 0.6rem;
        }

        /* ---------- Result ---------- */
        .t-result {
            padding: 0 0 4rem;
            scroll-margin-top: 110px;
        }

        /* ---------- How it works (no result yet) ---------- */
        .t-how {
            padding: 1rem 0 4rem;
        }

        .t-how ol {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            border-top: 2px solid var(--ink);
        }

        .t-how li {
            padding: 1.75rem 2rem 0 0;
        }

        .t-how li + li {
            padding-left: 2rem;
            border-left: 1px solid var(--line);
        }

        .t-how-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--orange);
        }

        .t-how h2 {
            font-family: var(--display);
            font-stretch: 105%;
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0.45rem 0 0.5rem;
        }

        .t-how p {
            color: var(--ink-2);
            font-size: 0.97rem;
        }

        .t-how p a {
            color: var(--teal-deep);
            font-weight: 700;
        }

        /* ---------- Help ---------- */
        .t-help {
            padding: 0 0 5rem;
        }

        .t-help-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1.5rem 3rem;
            align-items: center;
            background: var(--ink);
            color: #fff;
            border-radius: 24px;
            padding: 2.25rem 2.5rem;
        }

        .t-help-card h2 {
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.4rem;
        }

        .t-help-card p {
            color: #c4d1d5;
        }

        .t-help-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .t-help-actions .h-btn--ghost {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            background: transparent;
        }

        .t-help-actions .h-btn--ghost:hover {
            border-color: #fff;
        }

        @media (max-width: 860px) {
            .t-how ol {
                grid-template-columns: 1fr;
                border-top: none;
            }

            .t-how li,
            .t-how li + li {
                padding: 1.4rem 0;
                border-left: none;
                border-top: 2px solid var(--ink);
            }

            .t-help-card {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .t-hero {
                padding: 2.25rem 0 2.25rem;
            }

            .t-search {
                padding: 1.1rem;
            }

            .t-search-row {
                flex-direction: column;
            }

            .t-search input {
                flex: none;
                width: 100%;
            }

            .t-search .h-btn {
                width: 100%;
            }

            .t-help-card {
                padding: 1.75rem 1.35rem;
            }

            .t-help-actions,
            .t-help-actions .h-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Track Shipment', 'url' => null]]])
<div class="ui-page">

    {{-- ============ SEARCH ============ --}}
    <section class="t-hero">
        <div class="h-wrap">
            <div class="t-hero-inner">
                <span class="h-eyebrow">Track a shipment</span>
                <h1 class="h-display">Where's my cargo?</h1>
                <p class="h-lead">Enter your tracking or serial number to see where your shipment is and every stage it has passed through. No login needed.</p>

                @if(session('error'))
                    <div class="t-alert t-alert--error" role="alert"><i class="fas fa-circle-exclamation" aria-hidden="true"></i><p>{{ session('error') }}</p></div>
                @endif
                @if(session('info'))
                    <div class="t-alert t-alert--info" role="status"><i class="fas fa-circle-info" aria-hidden="true"></i><p>{{ session('info') }}</p></div>
                @endif

                <form class="t-search" method="POST" action="{{ route('track.check') }}">
                    @csrf
                    <label for="trackingNumber">Tracking / serial number</label>
                    <div class="t-search-row">
                        <input id="trackingNumber" type="text" name="tracking_number" required autocomplete="off" spellcheck="false"
                            placeholder="e.g. DUR.37977"
                            value="{{ old('tracking_number', $hasShipment ? $trackingRef : (session('tracking_attempt') ?? '')) }}"
                            @error('tracking_number') aria-invalid="true" aria-describedby="trackingError" @enderror
                            @unless($hasShipment) autofocus @endunless>
                        <button type="submit" class="h-btn h-btn--primary"><i class="fas fa-magnifying-glass" aria-hidden="true"></i> Track</button>
                    </div>
                    @error('tracking_number')
                        <p class="t-field-error" id="trackingError">{{ $message }}</p>
                    @enderror
                    <p class="t-hint">You'll find it in your shipment emails and on your invoice — for example <code>DUR.37977</code> or <code>773421428627451</code>.</p>
                </form>
            </div>
        </div>
    </section>

    @if($hasShipment)
    {{-- ============ RESULT ============ --}}
    <section class="t-result" id="results" aria-labelledby="resultTitle">
        <div class="h-wrap">
            @include('partials.shipment-card', ['shipment' => $shipment])
        </div>
    </section>
    @else
    {{-- ============ HOW IT WORKS ============ --}}
    <section class="t-how" aria-label="How tracking works">
        <div class="h-wrap">
            <ol>
                <li>
                    <span class="t-how-num">01</span>
                    <h2>Enter your number</h2>
                    <p>Use the tracking or serial number from your shipment emails or invoice.</p>
                </li>
                <li>
                    <span class="t-how-num">02</span>
                    <h2>See every stage</h2>
                    <p>From the China warehouse to customs to collection — with dates and locations.</p>
                </li>
                <li>
                    <span class="t-how-num">03</span>
                    <h2>Get updates by email</h2>
                    <p>We email you each time the status changes. Customers can also <a href="{{ route('login') }}">log in</a> to see all their shipments.</p>
                </li>
            </ol>
        </div>
    </section>
    @endif

    {{-- ============ HELP ============ --}}
    <section class="t-help" aria-labelledby="helpTitle">
        <div class="h-wrap">
            <div class="t-help-card">
                <div>
                    <h2 id="helpTitle">{{ $hasShipment ? 'Questions about this shipment?' : 'Can’t find your shipment?' }}</h2>
                    <p>Our Lusaka team can help — call, email, or message us on WhatsApp{{ $hasShipment ? ' and we’ll look it up for you' : ' with your name and phone number' }}.</p>
                </div>
                <div class="t-help-actions">
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-btn h-btn--primary"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
                    <a href="tel:+260572788685" class="h-btn h-btn--ghost"><i class="fas fa-phone" aria-hidden="true"></i> +260 572 788 685</a>
                    <a href="mailto:{{ $footerSections['contact_email'] ?? 'info@forusfreight.com' }}" class="h-btn h-btn--ghost"><i class="fas fa-envelope" aria-hidden="true"></i> Email</a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('scripts')
@if($hasShipment)
<script>
    // Bring the result into view after searching
    document.getElementById('results')?.scrollIntoView({ block: 'start' });
</script>
@endif
@endsection
