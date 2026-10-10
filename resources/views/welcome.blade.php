@extends('layouts.app')

@php
try {
    $page = \App\Models\CmsPage::where('slug', 'home')->first();
    $sections = $page?->sections ?? [];
    $footerSections = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
} catch (\Exception $e) {
    $page = null;
    $sections = [];
    $footerSections = [];
}
$whatsappNumber = $footerSections['whatsapp_number'] ?? '260572788685';
$whatsappLink = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode('Hi Forus Freight, I need a quote for logistics services');

$faqs = [
    [
        'q' => 'How long does shipping take?',
        'a' => 'It depends on the route and service. Road freight typically takes 3–5 days to neighbouring countries and 5–10 days across the SADC region, and air freight is available for urgent shipments (1–3 days). Your quote includes a specific timeframe.',
    ],
    [
        'q' => 'How does the free China address work?',
        'a' => 'Create a free account and verify your email and phone. Your personal address at our Foshan warehouse then appears in your account. Use it as the delivery address when you buy from Chinese stores; we receive your parcels, combine them and ship them to Lusaka.',
    ],
    [
        'q' => 'How do I track my shipment?',
        'a' => 'Enter your tracking number on our tracking page or log in to your account. We email you every time your shipment’s status changes, and send a text or WhatsApp message when it’s delivered.',
    ],
    [
        'q' => 'Do you handle customs clearance?',
        'a' => 'Yes. Customs clearance and documentation are part of our service, so your cargo is cleared before it is delivered or released for collection.',
    ],
    [
        'q' => 'What insurance options are available?',
        'a' => 'We offer insurance cover for shipments, with options for additional cover based on cargo value. Full details are included in your shipment quote.',
    ],
    [
        'q' => 'What payment methods do you accept?',
        'a' => 'We accept bank transfer and mobile money. Contact us for full payment details for your shipment.',
    ],
];

// Real tracking statuses (config/forus.php) used by the animated example shipment card
$exampleStatuses = collect(['RECEIVED_CN', 'CONSOLIDATED', 'DEPARTED_CN', 'IN_TRANSIT', 'CUSTOMS_CLEARANCE', 'READY_FOR_COLLECTION'])
    ->map(fn ($code) => [
        'label' => config('forus.tracking_statuses.' . $code, $code),
        'progress' => config('forus.tracking_status_progress.' . $code, 0),
    ])->values();
@endphp

@section('title', 'Forus Freight — Freight to and from Zambia, handled end to end')
@section('meta_description', 'Lusaka-based freight forwarder. Road, air and sea freight with customs clearance between Zambia, South Africa, China and the UAE — plus a free China warehouse address for online shoppers. Request a free quote.')

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        /* ---------- Hero ---------- */
        .h-hero {
            padding: 3.5rem 0 3.5rem;
            border-bottom: 1px solid var(--line);
        }

        .h-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            gap: 3.5rem;
            align-items: center;
        }

        .h-hero h1 {
            font-size: clamp(2.2rem, 3.9vw, 3.4rem);
            margin: 1.1rem 0 1.25rem;
        }

        .h-hero .h-lead {
            max-width: 34rem;
            margin-bottom: 1.75rem;
        }

        .h-choices {
            display: grid;
            border-top: 1px solid var(--line-strong);
            margin-bottom: 1.75rem;
        }

        .h-choice {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 1.25rem;
            padding: 1.15rem 0.25rem;
            border-bottom: 1px solid var(--line-strong);
            color: var(--ink);
            text-decoration: none;
            transition: background-color 0.2s ease, padding 0.2s ease;
        }

        .h-choice:hover {
            background: rgba(255, 255, 255, 0.6);
            padding-left: 0.9rem;
            padding-right: 0.9rem;
        }

        .h-choice-tag {
            font-family: var(--mono);
            font-size: 0.75rem;
            color: var(--muted);
            width: 1.5rem;
        }

        .h-choice-title {
            display: block;
            font-family: var(--display);
            font-stretch: 105%;
            font-weight: 700;
            font-size: 1.3rem;
            line-height: 1.25;
        }

        .h-choice-sub {
            display: block;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .h-choice-go {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--ink);
            color: #fff;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .h-choice--primary .h-choice-go {
            background: var(--orange);
            color: var(--ink);
        }

        .h-choice:hover .h-choice-go {
            transform: translateX(3px);
        }

        .h-track {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            max-width: 34rem;
        }

        .h-track label {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--ink-2);
            white-space: nowrap;
            margin-right: 0.25rem;
        }

        .h-track input {
            flex: 1;
            min-width: 0;
            height: 46px;
            padding: 0 0.9rem;
            border: 1px solid var(--line-strong);
            border-radius: 10px;
            background: #fff;
            font-family: var(--mono);
            font-size: 0.95rem;
            color: var(--ink);
        }

        .h-track input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.18);
        }

        .h-track button {
            height: 46px;
            padding: 0 1rem;
            border: none;
            border-radius: 10px;
            background: var(--ink);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .h-track button:hover {
            background: #000;
        }

        /* Hero visual: route map + live-style example shipment */
        .h-visual {
            position: relative;
            padding-bottom: 3.5rem;
        }

        .route-map {
            position: relative;
            aspect-ratio: 1000 / 560;
            border-radius: var(--radius);
            overflow: hidden;
            background:
                radial-gradient(ellipse at 32% 72%, rgba(0, 150, 150, 0.38), transparent 55%),
                linear-gradient(160deg, #0c2a33 0%, #071b22 100%);
        }

        .route-map-dots,
        .route-map-routes {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .route-map-dots {
            opacity: 0.42;
        }

        .route-line {
            fill: none;
            stroke: rgba(94, 234, 212, 0.35);
            stroke-width: 1.6;
            stroke-dasharray: 3 6;
        }

        .route-flow {
            fill: none;
            stroke: var(--orange);
            stroke-width: 2.6;
            stroke-linecap: round;
            stroke-dasharray: 60 640;
            animation: routeFlow 3.5s linear infinite;
            opacity: 0.9;
        }

        .route[data-hub="uae"] .route-flow { animation-delay: -1.2s; animation-duration: 4.5s; }
        .route[data-hub="china"] .route-flow { animation-delay: -2.4s; animation-duration: 6s; }

        @keyframes routeFlow {
            from { stroke-dashoffset: 700; }
            to { stroke-dashoffset: 0; }
        }

        .hub-dot {
            fill: var(--orange);
            stroke: #fff;
            stroke-width: 2;
        }

        .hub--hq .hub-dot { fill: var(--teal-bright); }

        .hub-pulse {
            fill: none;
            stroke: var(--orange);
            stroke-width: 2;
            transform-box: fill-box;
            transform-origin: center;
            animation: hubPulse 2.6s ease-out infinite;
        }

        .hub--hq .hub-pulse { stroke: var(--teal-bright); }

        @keyframes hubPulse {
            0% { transform: scale(0.7); opacity: 0.9; }
            100% { transform: scale(2.8); opacity: 0; }
        }

        .route-map.is-paused *,
        .h-ticket.is-paused * {
            animation-play-state: paused !important;
        }

        .route-label {
            position: absolute;
            font-family: var(--mono);
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            color: #fff;
            line-height: 1.25;
            white-space: nowrap;
            text-shadow: 0 1px 8px rgba(0, 0, 0, 0.6);
            pointer-events: none;
        }

        .route-label b {
            font-weight: 500;
            color: #fff;
        }

        .route-label small {
            display: block;
            font-size: 0.68rem;
            color: #a9bcc2;
        }

        .route-label--left { transform: translate(calc(-100% - 14px), -50%); text-align: right; }
        .route-label--right { transform: translate(14px, -50%); }
        .route-label--top { transform: translate(-50%, calc(-100% - 12px)); text-align: center; }
        .route-label--hq b { color: #5eead4; }

        .h-ticket {
            position: absolute;
            right: -1.25rem;
            bottom: 0;
            width: min(290px, 70%);
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--line);
            box-shadow: 0 24px 50px rgba(13, 27, 32, 0.22);
            padding: 1.1rem 1.2rem 1rem;
        }

        .h-ticket-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--mono);
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.65rem;
        }

        .h-ticket-lane {
            color: var(--ink);
            font-weight: 500;
        }

        .h-ticket-status {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-weight: 700;
            font-size: 1.02rem;
            min-height: 1.6em;
        }

        .h-ticket-live {
            position: relative;
            flex-shrink: 0;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--orange);
        }

        .h-ticket-live::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid var(--orange);
            animation: livePing 1.8s ease-out infinite;
        }

        @keyframes livePing {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(1.9); opacity: 0; }
        }

        .h-ticket-text {
            transition: opacity 0.25s ease;
        }

        .h-ticket-bar {
            height: 6px;
            border-radius: 6px;
            background: var(--paper-2);
            margin: 0.8rem 0 0.7rem;
            overflow: hidden;
        }

        .h-ticket-bar span {
            display: block;
            height: 100%;
            width: 20%;
            border-radius: 6px;
            background: linear-gradient(90deg, var(--teal), var(--teal-bright));
            transition: width 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .h-ticket-foot {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: var(--muted);
        }

        .h-ticket-foot i {
            color: var(--teal);
            margin-right: 0.3rem;
        }

        /* ---------- Capability strip ---------- */
        .h-strip {
            border-bottom: 1px solid var(--line);
            background: var(--paper);
        }

        .h-strip ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.75rem 2rem;
            padding: 1.35rem 0;
            font-family: var(--mono);
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--ink-2);
        }

        .h-strip li {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .h-strip i {
            color: var(--teal);
        }

        /* ---------- Section scaffolding ---------- */
        .h-section {
            padding: 6.5rem 0;
        }

        .h-section-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 2rem 4rem;
            align-items: end;
            margin-bottom: 3.5rem;
        }

        .h-section-head .h-eyebrow {
            margin-bottom: 1rem;
        }

        /* ---------- Business freight ---------- */
        .h-services {
            list-style: none;
            border-top: 1px solid var(--line-strong);
        }

        .h-service {
            display: grid;
            grid-template-columns: 3.5rem minmax(0, 1.1fr) minmax(0, 1.4fr);
            gap: 1.5rem;
            align-items: baseline;
            padding: 1.6rem 0;
            border-bottom: 1px solid var(--line);
        }

        .h-service-num {
            font-family: var(--mono);
            font-size: 0.85rem;
            color: var(--muted);
        }

        .h-service h3 {
            font-family: var(--display);
            font-stretch: 105%;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .h-service p {
            color: var(--ink-2);
        }

        .h-section-cta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem 1.5rem;
            margin-top: 2.5rem;
        }

        .h-textlink {
            color: var(--teal-deep);
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 44px;
        }

        .h-textlink:hover {
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .h-sectors {
            margin-top: 2rem;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .h-sectors strong {
            color: var(--ink-2);
            font-weight: 600;
        }

        /* ---------- China shopping (dark) ---------- */
        .h-dark {
            background: var(--ink);
            color: #fff;
        }

        .h-dark .h-eyebrow {
            color: #5eead4;
        }

        .h-dark .h-lead {
            color: #c4d1d5;
        }

        .h-china-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 4.5rem;
            align-items: center;
        }

        .h-china-grid .h-h2 {
            margin: 1rem 0 1.25rem;
        }

        .h-stores {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1.75rem 0 2.25rem;
            list-style: none;
        }

        .h-stores li {
            font-family: var(--mono);
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #dfe8ea;
        }

        .h-steps {
            list-style: none;
            counter-reset: step;
            display: grid;
            gap: 0;
            margin-bottom: 2.5rem;
        }

        .h-steps li {
            display: grid;
            grid-template-columns: 3rem 1fr;
            gap: 1rem;
            padding: 1.1rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
        }

        .h-steps li:last-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        .h-steps li::before {
            counter-increment: step;
            content: '0' counter(step);
            font-family: var(--mono);
            color: var(--orange);
            font-size: 0.9rem;
            padding-top: 0.15rem;
        }

        .h-steps h3 {
            font-size: 1.08rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .h-steps p {
            color: #b5c4c9;
            font-size: 0.95rem;
        }

        /* Parcel label visual */
        .h-label-wrap {
            position: relative;
            padding: 2.5rem 1rem 1rem;
        }

        .h-label-wrap::before {
            /* a second label peeking out behind, like a stack of parcels */
            content: '';
            position: absolute;
            inset: 1rem 2.5rem 3.5rem 3rem;
            background: #20414a;
            border-radius: 14px;
            transform: rotate(-4deg);
        }

        .h-label {
            position: relative;
            background: #fffdf7;
            color: var(--ink);
            border-radius: 14px;
            padding: 1.5rem 1.6rem 1.25rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45);
            transform: rotate(1.5deg);
            font-family: var(--mono);
            font-size: 0.86rem;
            line-height: 1.55;
            max-width: 460px;
            margin: 0 auto;
        }

        .h-label-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.85rem;
            border-bottom: 2px solid var(--ink);
            margin-bottom: 1rem;
        }

        .h-label-brand {
            font-family: var(--display);
            font-stretch: 115%;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.02em;
        }

        .h-label-route {
            background: var(--ink);
            color: #fff;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.8rem;
        }

        .h-label-key {
            display: block;
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .h-label-mark {
            margin-bottom: 1rem;
        }

        .h-label-code {
            font-family: var(--display);
            font-stretch: 110%;
            font-weight: 800;
            font-size: 1.9rem;
            letter-spacing: 0.02em;
            color: var(--orange);
        }

        .h-label-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .h-blur {
            filter: blur(5px);
            user-select: none;
        }

        .h-label-barcode {
            height: 46px;
            background: repeating-linear-gradient(90deg,
                var(--ink) 0 2px, transparent 2px 5px,
                var(--ink) 5px 6px, transparent 6px 9px,
                var(--ink) 9px 12px, transparent 12px 14px);
            margin-bottom: 0.75rem;
            opacity: 0.9;
        }

        .h-label-foot {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .h-label-note {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.92rem;
            color: #c4d1d5;
        }

        .h-label-note i {
            color: var(--orange);
            margin-right: 0.4rem;
        }

        .h-dark .h-textlink {
            color: #5eead4;
        }

        /* ---------- How a shipment moves ---------- */
        .h-flow {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            border-top: 2px solid var(--ink);
        }

        .h-flow li {
            position: relative;
            padding: 1.75rem 1.5rem 0 0;
        }

        .h-flow li::before {
            content: '';
            position: absolute;
            top: -7px;
            left: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--paper);
            border: 2px solid var(--ink);
        }

        .h-flow li:last-child::before {
            background: var(--orange);
            border-color: var(--orange);
        }

        .h-flow-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--muted);
        }

        .h-flow h3 {
            font-family: var(--display);
            font-stretch: 105%;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0.4rem 0 0.5rem;
        }

        .h-flow p {
            color: var(--ink-2);
            font-size: 0.97rem;
        }

        .h-flow-status {
            display: inline-block;
            margin-top: 0.9rem;
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.03em;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            background: var(--paper-2);
            color: var(--ink-2);
        }

        .h-updates {
            margin-top: 3.5rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            background: var(--line);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .h-update {
            background: #fff;
            padding: 1.5rem 1.6rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .h-update i {
            font-size: 1.15rem;
            color: var(--teal);
            margin-top: 0.2rem;
            width: 1.25rem;
            text-align: center;
        }

        .h-update h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .h-update p {
            font-size: 0.92rem;
            color: var(--muted);
        }

        /* ---------- Where we operate ---------- */
        .h-places {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .h-place {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
        }

        .h-place-code {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--teal-deep);
            letter-spacing: 0.08em;
        }

        .h-place h3 {
            font-family: var(--display);
            font-stretch: 105%;
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0.35rem 0 0.75rem;
        }

        .h-place address {
            font-style: normal;
            color: var(--ink-2);
            font-size: 0.95rem;
            line-height: 1.55;
        }

        .h-place-meta {
            margin-top: auto;
            padding-top: 1.25rem;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .h-place-meta a {
            color: var(--teal-deep);
            font-weight: 600;
        }

        /* ---------- FAQ ---------- */
        .h-faq-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.2fr);
            gap: 4rem;
            align-items: start;
        }

        .h-faq-grid .h-lead {
            margin-top: 1rem;
        }

        .h-faq-aside {
            position: sticky;
            top: 130px;
        }

        .h-faq-list {
            border-top: 1px solid var(--line-strong);
        }

        .h-faq-list details {
            border-bottom: 1px solid var(--line-strong);
        }

        .h-faq-list summary {
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.35rem 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.08rem;
        }

        .h-faq-list summary::-webkit-details-marker {
            display: none;
        }

        .h-faq-list summary::after {
            content: '+';
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--line-strong);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: var(--mono);
            font-size: 1.1rem;
            font-weight: 400;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .h-faq-list details[open] summary::after {
            transform: rotate(45deg);
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .h-faq-list details p {
            color: var(--ink-2);
            padding: 0 3rem 1.5rem 0;
        }

        /* ---------- Final CTA ---------- */
        .h-final {
            padding: 0 0 6.5rem;
        }

        .h-final-card {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background: linear-gradient(135deg, #006b6b 0%, #00504f 100%);
            color: #fff;
            border-radius: 28px;
            padding: 4.5rem;
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
            gap: 3rem;
            align-items: end;
        }

        .h-final-card::before {
            /* the network map again, faintly, to close the page where it opened */
            content: '';
            position: absolute;
            inset: -10% -15% -10% 35%;
            background: url('{{ asset('images/network-map-dots.svg') }}') center / contain no-repeat;
            filter: brightness(3);
            opacity: 0.14;
            z-index: -1;
        }

        .h-final-card .h-h2 {
            font-size: clamp(2.2rem, 4.6vw, 3.6rem);
        }

        .h-final-card p {
            color: #d7ecec;
            margin-top: 1rem;
            max-width: 34rem;
        }

        .h-final-actions {
            display: grid;
            gap: 0.75rem;
        }

        .h-final-actions .h-btn {
            width: 100%;
        }

        .h-final-phone {
            text-align: center;
            font-family: var(--mono);
            font-size: 0.85rem;
            color: #d7ecec;
            margin-top: 0.25rem;
        }

        .h-final-phone a {
            color: #fff;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 1024px) {
            .h-hero-grid,
            .h-china-grid,
            .h-faq-grid,
            .h-final-card {
                grid-template-columns: 1fr;
            }

            .h-hero-grid {
                gap: 3rem;
            }

            .h-hero h1 {
                font-size: clamp(2rem, 6vw, 3.2rem);
            }

            .h-ticket {
                right: 1rem;
            }

            .h-section-head {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .h-flow {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                border-top: none;
                gap: 0 1.5rem;
            }

            .h-flow li {
                border-top: 2px solid var(--ink);
                margin-top: 1.5rem;
            }

            .h-updates,
            .h-places {
                grid-template-columns: 1fr;
            }

            .h-final-card {
                padding: 3rem;
            }

            .h-faq-aside {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .h-wrap {
                padding: 0 1.1rem;
            }

            .h-hero {
                padding: 2.5rem 0 2.5rem;
            }

            .h-section {
                padding: 4.5rem 0;
            }

            .h-service {
                grid-template-columns: 2.5rem 1fr;
                gap: 0.35rem 1rem;
            }

            .h-service p {
                grid-column: 2;
            }

            .h-track {
                flex-wrap: wrap;
            }

            .h-track label {
                width: 100%;
            }

            .h-strip ul {
                justify-content: flex-start;
                gap: 0.6rem 1.5rem;
            }

            .h-label {
                transform: none;
                font-size: 0.8rem;
            }

            .h-label-wrap::before {
                inset: 1.5rem 1rem 3.5rem 1rem;
            }

            .route-label small {
                display: none;
            }

            .h-final {
                padding-bottom: 4.5rem;
            }

            .h-final-card {
                padding: 2.25rem 1.5rem;
                border-radius: 20px;
            }
        }

        @media (max-width: 560px) {
            .h-hero h1 {
                font-size: 1.95rem;
            }

            .h-flow {
                grid-template-columns: 1fr;
            }

            .h-choice {
                grid-template-columns: 1fr auto;
                gap: 1rem;
            }

            .h-choice-tag {
                display: none;
            }

            .h-choice-title {
                font-size: 1.12rem;
            }

            .route-label {
                font-size: 0.62rem;
            }

            .route-label--left { transform: translate(calc(-100% - 8px), -50%); }
            .route-label--right { transform: translate(8px, -50%); }
            .route-label--top { transform: translate(-50%, calc(-100% - 7px)); }

            .h-visual {
                padding-bottom: 0;
            }

            .h-ticket {
                position: relative;
                right: auto;
                width: auto;
                margin: 0.75rem 0 0;
            }

            .h-section-cta .h-btn,
            .h-china-grid .h-btn {
                width: 100%;
            }

            .h-faq-list details p {
                padding-right: 0;
            }
        }
    </style>
@endsection

@section('content')
<div class="home">

    {{-- ============ HERO ============ --}}
    <section class="h-hero">
        <div class="h-wrap h-hero-grid">
            <div>
                <span class="h-eyebrow">Freight forwarder &middot; Lusaka, Zambia</span>
                <h1 class="h-display">{{ $sections['hero_title'] ?? 'Freight to and from Zambia, handled end to end' }}</h1>
                <p class="h-lead">{{ $sections['hero_subtitle'] ?? 'Road, air and sea freight with customs clearance between Zambia, South Africa, China and the UAE — or shop from China and collect in Lusaka.' }}</p>

                <nav class="h-choices" aria-label="Choose how you want to ship">
                    <a href="{{ $sections['hero_cta_link'] ?? route('quote') }}" class="h-choice h-choice--primary">
                        <span class="h-choice-tag">A</span>
                        <span>
                            <span class="h-choice-title">{{ $sections['hero_cta_text'] ?? 'Get a free freight quote' }}</span>
                            <span class="h-choice-sub">For businesses moving cargo by road, air or sea</span>
                        </span>
                        <span class="h-choice-go" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
                    </a>
                    <a href="{{ auth()->check() ? route('client.getting-started') : route('register') }}" class="h-choice">
                        <span class="h-choice-tag">B</span>
                        <span>
                            <span class="h-choice-title">Get your free China address</span>
                            <span class="h-choice-sub">For shoppers buying from Chinese stores</span>
                        </span>
                        <span class="h-choice-go" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </nav>

                <form class="h-track" method="POST" action="{{ route('track.check') }}">
                    @csrf
                    <label for="heroTrackNumber">Already shipping?</label>
                    <input id="heroTrackNumber" type="text" name="tracking_number" placeholder="Tracking number" autocomplete="off" required>
                    <button type="submit">Track</button>
                </form>
            </div>

            <div class="h-visual">
                <figure class="route-map" id="routeMap" aria-labelledby="routeMapCaption">
                    <img class="route-map-dots" src="{{ asset('images/network-map-dots.svg') }}" alt="" width="1000" height="560" decoding="async">
                    <svg class="route-map-routes" viewBox="0 0 1000 560" aria-hidden="true" focusable="false">
                        <defs>
                            <radialGradient id="packetGlow">
                                <stop offset="0%" stop-color="#ffffff"/>
                                <stop offset="45%" stop-color="#ffb37f"/>
                                <stop offset="100%" stop-color="#ff6200" stop-opacity="0"/>
                            </radialGradient>
                        </defs>
                        <g class="route" data-hub="south-africa">
                            <path id="routeDurban" class="route-line" d="M323.8 508.4 Q372 462 313.6 410.7"/>
                            <path class="route-flow" d="M323.8 508.4 Q372 462 313.6 410.7"/>
                            <circle r="8" fill="url(#packetGlow)"><animateMotion dur="3.2s" repeatCount="indefinite"><mpath href="#routeDurban"/></animateMotion></circle>
                        </g>
                        <g class="route" data-hub="uae">
                            <path id="routeDubai" class="route-line" d="M462.8 136.9 Q470 300 313.6 410.7"/>
                            <path class="route-flow" d="M462.8 136.9 Q470 300 313.6 410.7"/>
                            <circle r="8" fill="url(#packetGlow)"><animateMotion dur="4.2s" begin="0.8s" repeatCount="indefinite"><mpath href="#routeDubai"/></animateMotion></circle>
                        </g>
                        <g class="route" data-hub="china">
                            <path id="routeFoshan" class="route-line" d="M792.5 151.6 Q640 430 313.6 410.7"/>
                            <path class="route-flow" d="M792.5 151.6 Q640 430 313.6 410.7"/>
                            <circle r="8" fill="url(#packetGlow)"><animateMotion dur="5.6s" begin="1.6s" repeatCount="indefinite"><mpath href="#routeFoshan"/></animateMotion></circle>
                        </g>
                        <g class="hub hub--hq" transform="translate(313.6 410.7)"><circle class="hub-pulse" r="11"/><circle class="hub-dot" r="7.5"/></g>
                        <g class="hub" transform="translate(323.8 508.4)"><circle class="hub-pulse" r="8"/><circle class="hub-dot" r="5.5"/></g>
                        <g class="hub" transform="translate(462.8 136.9)"><circle class="hub-pulse" r="8"/><circle class="hub-dot" r="5.5"/></g>
                        <g class="hub" transform="translate(792.5 151.6)"><circle class="hub-pulse" r="8"/><circle class="hub-dot" r="5.5"/></g>
                    </svg>
                    <span class="route-label route-label--left route-label--hq" style="left: 31.36%; top: 73.34%;"><b>LUSAKA</b> <small>HQ &amp; warehouse</small></span>
                    <span class="route-label route-label--right" style="left: 32.38%; top: 90.79%;"><b>DUR</b> <small>Durban</small></span>
                    <span class="route-label route-label--top" style="left: 46.28%; top: 24.45%;"><b>DXB</b> <small>Dubai</small></span>
                    <span class="route-label route-label--top" style="left: 79.25%; top: 27.07%;"><b>FUO</b> <small>Foshan &middot; warehouse</small></span>
                    <figcaption id="routeMapCaption" class="sr-only">Map of Forus Freight's network: freight moves into Lusaka, Zambia from Durban in South Africa, Dubai in the UAE and our warehouse in Foshan, China.</figcaption>
                </figure>

                <div class="h-ticket" id="exampleTicket" aria-hidden="true">
                    <div class="h-ticket-head">
                        <span>Example shipment</span>
                        <span class="h-ticket-lane">FUO &rarr; LUSAKA</span>
                    </div>
                    <div class="h-ticket-status">
                        <span class="h-ticket-live"></span>
                        <span class="h-ticket-text" id="ticketText">{{ $exampleStatuses[3]['label'] }}</span>
                    </div>
                    <div class="h-ticket-bar"><span id="ticketBar" style="width: {{ $exampleStatuses[3]['progress'] }}%;"></span></div>
                    <div class="h-ticket-foot">
                        <span><i class="fas fa-envelope" aria-hidden="true"></i>Email update sent</span>
                        <span id="ticketPct">{{ $exampleStatuses[3]['progress'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ CAPABILITIES ============ --}}
    <div class="h-strip">
        <div class="h-wrap">
            <ul aria-label="What we handle">
                <li><i class="fas fa-truck" aria-hidden="true"></i> Road</li>
                <li><i class="fas fa-plane" aria-hidden="true"></i> Air</li>
                <li><i class="fas fa-ship" aria-hidden="true"></i> Sea</li>
                <li><i class="fas fa-file-signature" aria-hidden="true"></i> Customs clearance</li>
                <li><i class="fas fa-warehouse" aria-hidden="true"></i> Warehousing</li>
                <li><i class="fas fa-boxes-stacked" aria-hidden="true"></i> Parcel consolidation</li>
            </ul>
        </div>
    </div>

    {{-- ============ FOR BUSINESSES ============ --}}
    <section class="h-section" id="business-freight" aria-labelledby="bizTitle">
        <div class="h-wrap">
            <div class="h-section-head">
                <div>
                    <span class="h-eyebrow">For businesses</span>
                    <h2 class="h-h2" id="bizTitle">One team for your cargo, from pickup to Lusaka</h2>
                </div>
                <p class="h-lead">Tell us what you're moving and where. We arrange transport, paperwork and clearance, and keep you updated until it's delivered or ready to collect.</p>
            </div>

            <ol class="h-services">
                <li class="h-service">
                    <span class="h-service-num">01</span>
                    <h3>Road freight</h3>
                    <p>Cross-border trucking between Zambia, South Africa and the wider SADC region — typically 3–5 days to neighbouring countries.</p>
                </li>
                <li class="h-service">
                    <span class="h-service-num">02</span>
                    <h3>Air freight</h3>
                    <p>For urgent or high-value cargo, usually 1–3 days. Ideal for spares, samples and time-critical stock.</p>
                </li>
                <li class="h-service">
                    <span class="h-service-num">03</span>
                    <h3>Sea freight</h3>
                    <p>Container and loose cargo from China and the UAE, cleared at the port and moved inland to Zambia.</p>
                </li>
                <li class="h-service">
                    <span class="h-service-num">04</span>
                    <h3>Customs clearance</h3>
                    <p>Documentation and clearance handled for you, so your goods aren't left waiting at the border.</p>
                </li>
                <li class="h-service">
                    <span class="h-service-num">05</span>
                    <h3>Warehousing</h3>
                    <p>Storage at our Makeni warehouse in Lusaka, with collection or onward delivery when you need it.</p>
                </li>
                <li class="h-service">
                    <span class="h-service-num">06</span>
                    <h3>Bulk &amp; project cargo</h3>
                    <p>Large, heavy or out-of-gauge loads for mining, construction and industrial projects.</p>
                </li>
            </ol>

            <p class="h-sectors"><strong>We move cargo for</strong> mining &amp; resources, construction, retail &amp; fashion, electronics, healthcare, automotive and food &amp; beverage businesses.</p>

            <div class="h-section-cta">
                <a href="{{ route('quote') }}" class="h-btn h-btn--primary">Get a free freight quote <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-textlink"><i class="fab fa-whatsapp" aria-hidden="true"></i> Ask a question on WhatsApp</a>
            </div>
        </div>
    </section>

    {{-- ============ FOR ONLINE SHOPPERS ============ --}}
    <section class="h-section h-dark" id="shop-from-china" aria-labelledby="chinaTitle">
        <div class="h-wrap h-china-grid">
            <div>
                <span class="h-eyebrow">For online shoppers</span>
                <h2 class="h-h2" id="chinaTitle">Shop from China. Collect in Lusaka.</h2>
                <p class="h-lead">Get your own address at our warehouse in Foshan, China — free. Ship your online orders there and we'll bring them home to Zambia.</p>

                <ul class="h-stores" aria-label="Stores you can buy from">
                    <li>Alibaba</li>
                    <li>Amazon</li>
                    <li>eBay</li>
                    <li>Shein</li>
                    <li>Temu</li>
                    <li>&amp; more</li>
                </ul>

                <ol class="h-steps">
                    <li>
                        <div>
                            <h3>Create your free account</h3>
                            <p>About two minutes. Verify your email and phone, and your China address appears in your account.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <h3>Use it as your delivery address</h3>
                            <p>Check out as normal. Our Foshan team receives every parcel on your behalf.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <h3>We combine, ship and track</h3>
                            <p>Parcels are consolidated, shipped to Zambia and tracked until they're ready at our Lusaka warehouse.</p>
                        </div>
                    </li>
                </ol>

                <div class="h-section-cta" style="margin-top: 0;">
                    @guest
                        <a href="{{ route('register') }}" class="h-btn h-btn--primary">Get my free China address <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <a href="{{ route('login') }}" class="h-textlink">I already have an account</a>
                    @else
                        <a href="{{ route('client.getting-started') }}" class="h-btn h-btn--primary">View my China address <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    @endguest
                </div>
            </div>

            <figure class="h-label-wrap" aria-labelledby="labelCaption">
                <div class="h-label" aria-hidden="true">
                    <div class="h-label-top">
                        <span class="h-label-brand">FORUS FREIGHT</span>
                        <span class="h-label-route">FUO &rarr; LUSAKA</span>
                    </div>
                    <div class="h-label-mark">
                        <span class="h-label-key">Shipping mark</span>
                        <span class="h-label-code">ZMFFL <span class="h-blur">406118</span></span>
                    </div>
                    <div class="h-label-row">
                        <span class="h-label-key">Deliver to</span>
                        <span class="h-blur">Your name &middot; +260 97 000 0000</span>
                        <span>Forus Freight Warehouse, B18</span>
                        <span>Lijin Logistics Park</span>
                        <span class="h-blur">No. 3 Yanjiang Road, Dabu, Lishui Town</span>
                        <span>Nanhai District, Foshan, China</span>
                    </div>
                    <div class="h-label-barcode"></div>
                    <div class="h-label-foot">
                        <span>Consolidated &middot; Tracked</span>
                        <span>Ready at Lusaka warehouse</span>
                    </div>
                </div>
                <figcaption id="labelCaption" class="h-label-note"><i class="fas fa-lock" aria-hidden="true"></i> Your personal shipping mark and full address unlock after free sign-up.</figcaption>
            </figure>
        </div>
    </section>

    {{-- ============ HOW IT MOVES ============ --}}
    <section class="h-section" aria-labelledby="flowTitle">
        <div class="h-wrap">
            <div class="h-section-head">
                <div>
                    <span class="h-eyebrow">How it works</span>
                    <h2 class="h-h2" id="flowTitle">You always know where your cargo is</h2>
                </div>
                <p class="h-lead">Every shipment gets a tracking number and moves through clear stages. You can check it any time, and we tell you when anything changes.</p>
            </div>

            <ol class="h-flow">
                <li>
                    <span class="h-flow-num">01</span>
                    <h3>Request a quote</h3>
                    <p>Share the weight, size and route. We reply with a price and an estimated transit time.</p>
                </li>
                <li>
                    <span class="h-flow-num">02</span>
                    <h3>We collect &amp; dispatch</h3>
                    <p>Pickup, consolidation and documentation are arranged at origin.</p>
                    <span class="h-flow-status">{{ config('forus.tracking_statuses.DEPARTED_CN', 'Departed China') }}</span>
                </li>
                <li>
                    <span class="h-flow-num">03</span>
                    <h3>Cleared into Zambia</h3>
                    <p>We handle customs so your cargo moves on without delays at the border.</p>
                    <span class="h-flow-status">{{ config('forus.tracking_statuses.CLEARED', 'Customs Cleared') }}</span>
                </li>
                <li>
                    <span class="h-flow-num">04</span>
                    <h3>Delivered or collected</h3>
                    <p>We deliver to your door, or you collect from our Lusaka warehouse.</p>
                    <span class="h-flow-status">{{ config('forus.tracking_statuses.READY_FOR_COLLECTION', 'Ready for Collection') }}</span>
                </li>
            </ol>

            <div class="h-updates">
                <div class="h-update">
                    <i class="fas fa-magnifying-glass-location" aria-hidden="true"></i>
                    <div>
                        <h3>Online tracking</h3>
                        <p>Look up any shipment with its tracking number — no login needed.</p>
                    </div>
                </div>
                <div class="h-update">
                    <i class="fas fa-envelope-open-text" aria-hidden="true"></i>
                    <div>
                        <h3>Email at every step</h3>
                        <p>We email you each time your shipment's status changes.</p>
                    </div>
                </div>
                <div class="h-update">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <div>
                        <h3>Text when it lands</h3>
                        <p>A text or WhatsApp message when your shipment is delivered.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ WHERE WE OPERATE ============ --}}
    <section class="h-section" style="padding-top: 0;" aria-labelledby="placesTitle">
        <div class="h-wrap">
            <div class="h-section-head">
                <div>
                    <span class="h-eyebrow">Where we operate</span>
                    <h2 class="h-h2" id="placesTitle">Real people, real addresses</h2>
                </div>
                <p class="h-lead">Visit our office, collect from our warehouse, or message the team on WhatsApp. You'll always deal with someone who knows your shipment.</p>
            </div>

            <div class="h-places">
                <div class="h-place">
                    <span class="h-place-code">LUSAKA &middot; OFFICE</span>
                    <h3>Lusaka head office</h3>
                    <address>{{ $footerSections['contact_address'] ?? 'Metrolux Plaza, Plot No. 401A/8 Kafue Road' }}<br>{{ $footerSections['contact_city'] ?? 'Lusaka, Zambia' }}</address>
                    <p class="h-place-meta">Mon–Fri, 08:00–17:00 &middot; <a href="tel:+260572788685">+260 572 788 685</a></p>
                </div>
                <div class="h-place">
                    <span class="h-place-code">LUSAKA &middot; WAREHOUSE</span>
                    <h3>Lusaka warehouse</h3>
                    <address>{{ $footerSections['contact_warehouse_address'] ?? 'Plot No. F/26/397a, Robert ‘B’ Warehouse ‘A’, Off Kafue Road, Makeni' }}<br>{{ $footerSections['contact_warehouse_city'] ?? 'Lusaka, Zambia' }}</address>
                    <p class="h-place-meta">Collection point for China orders and stored cargo</p>
                </div>
                <div class="h-place">
                    <span class="h-place-code">FUO &middot; WAREHOUSE</span>
                    <h3>China consolidation hub</h3>
                    <address>Foshan, Guangdong<br>China</address>
                    <p class="h-place-meta">Your full personal address is shown in your account after <a href="{{ route('register') }}">free sign-up</a></p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="h-section" style="padding-top: 0;" aria-labelledby="faqTitle">
        <div class="h-wrap h-faq-grid">
            <div class="h-faq-aside">
                <span class="h-eyebrow">Questions</span>
                <h2 class="h-h2" id="faqTitle" style="margin-top: 1rem;">Good to know</h2>
                <p class="h-lead">Can't find your answer? <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-textlink" style="display: inline;">Message us on WhatsApp</a> or see the <a href="{{ route('faq') }}" class="h-textlink" style="display: inline;">full FAQ</a>.</p>
            </div>
            <div class="h-faq-list">
                @foreach ($faqs as $i => $faq)
                    <details @if($i === 0) open @endif>
                        <summary>{{ $faq['q'] }}</summary>
                        <p>{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ FINAL CTA ============ --}}
    <section class="h-final" aria-labelledby="finalTitle">
        <div class="h-wrap">
            <div class="h-final-card">
                <div>
                    <h2 class="h-h2" id="finalTitle">Got cargo to move?</h2>
                    <p>Tell us what, where and when. You'll get a clear price and transit time — free, with no obligation.</p>
                </div>
                <div class="h-final-actions">
                    <a href="{{ route('quote') }}" class="h-btn h-btn--primary">Get a free quote <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-btn h-btn--light"><i class="fab fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
                    <p class="h-final-phone">or call <a href="tel:+260572788685">+260 572 788 685</a></p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script>
    (function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

        // Route map: animate only while on screen; static under reduced motion.
        const routeMap = document.getElementById('routeMap');
        const ticket = document.getElementById('exampleTicket');
        let mapVisible = false;

        function syncMotion() {
            const paused = reduceMotion.matches || !mapVisible;
            if (routeMap) {
                routeMap.classList.toggle('is-paused', paused);
                const svg = routeMap.querySelector('svg');
                if (svg && svg.pauseAnimations) paused ? svg.pauseAnimations() : svg.unpauseAnimations();
            }
            if (ticket) ticket.classList.toggle('is-paused', paused);
        }

        if (routeMap && 'IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                mapVisible = entries[0].isIntersecting;
                syncMotion();
            }).observe(routeMap);
        } else {
            mapVisible = true;
        }
        if (reduceMotion.addEventListener) reduceMotion.addEventListener('change', syncMotion);
        syncMotion();

        // Example shipment card steps through real tracking statuses.
        const statuses = @json($exampleStatuses);
        const text = document.getElementById('ticketText');
        const bar = document.getElementById('ticketBar');
        const pct = document.getElementById('ticketPct');
        let step = 3;

        if (ticket && text && statuses.length) {
            setInterval(function () {
                if (reduceMotion.matches || !mapVisible || document.hidden) return;
                step = (step + 1) % statuses.length;
                text.style.opacity = 0;
                setTimeout(function () {
                    text.textContent = statuses[step].label;
                    text.style.opacity = 1;
                    bar.style.width = statuses[step].progress + '%';
                    pct.textContent = statuses[step].progress + '%';
                }, 250);
            }, 2800);
        }
    })();
</script>
@endsection
