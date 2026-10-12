@php
$page = \App\Models\CmsPage::where('slug', 'services')->first();
$sections = $page?->sections ?? [];
$footerSections = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
$whatsappLink = 'https://wa.me/' . ($footerSections['whatsapp_number'] ?? '260572788685') . '?text=' . urlencode('Hi Forus Freight, I have a question about your services');

// One entry per service. Anchors (id) are linked from the footer, the quote
// form and older pages, so keep existing ids stable.
$services = [
    [
        'id' => 'cross-border',
        'quote' => 'road-freight',
        'mode' => 'Road',
        'icon' => 'fa-truck',
        'title' => 'Road freight',
        'summary' => 'Cross-border trucking between Zambia, South Africa and the wider SADC region — full loads or shared space for smaller consignments.',
        'for' => ['Import & export businesses', 'Manufacturers and retail chains', 'Mining companies', 'Anyone moving goods across SADC borders'],
        'facts' => [
            'Neighbouring countries' => '3–5 days',
            'Across SADC' => '5–10 days',
            'Includes' => 'Border paperwork & tracking',
        ],
        'cta' => 'Get a road freight quote',
    ],
    [
        'id' => 'air-freight',
        'quote' => 'air-freight',
        'mode' => 'Air',
        'icon' => 'fa-plane',
        'title' => 'Air freight',
        'summary' => 'When time matters more than cost. Ideal for urgent stock, spare parts, samples and high-value goods.',
        'for' => ['Urgent replacement parts', 'High-value or fragile goods', 'Samples and small consignments', 'Time-critical retail stock'],
        'facts' => [
            'Typical transit' => '1–3 days',
            'Best for' => 'Urgent & high-value cargo',
        ],
        'cta' => 'Get an air freight quote',
    ],
    [
        'id' => 'sea-freight',
        'quote' => 'sea-freight',
        'mode' => 'Sea',
        'icon' => 'fa-ship',
        'title' => 'Sea freight',
        'summary' => 'The most economical way to move larger volumes from China and the UAE. We handle the port side and bring your cargo inland to Zambia.',
        'for' => ['Large or heavy shipments', 'Regular stock imports', 'Machinery and equipment', 'Cost-sensitive cargo'],
        'facts' => [
            'Cargo' => 'Full & shared containers, loose cargo',
            'Routes' => 'China & UAE → port → Zambia',
        ],
        'cta' => 'Get a sea freight quote',
    ],
    [
        'id' => 'customs',
        'quote' => 'customs',
        'mode' => 'Clearance',
        'icon' => 'fa-file-signature',
        'title' => 'Customs clearance',
        'summary' => 'We prepare the documentation and handle clearance so your goods don’t sit waiting at the border or the port.',
        'for' => ['First-time importers', 'Businesses importing regularly', 'Commercial and personal goods', 'Shipments with us or another carrier'],
        'facts' => [
            'Covers' => 'Documentation & duty processing',
            'Status updates' => 'Customs Clearance → Customs Cleared',
        ],
        'cta' => 'Ask about customs clearance',
    ],
    [
        'id' => 'warehousing',
        'quote' => 'warehousing',
        'mode' => 'Storage',
        'icon' => 'fa-warehouse',
        'title' => 'Warehousing & storage',
        'summary' => 'Secure storage at our Makeni warehouse in Lusaka — for a few days between shipments or as your distribution base.',
        'for' => ['E-commerce businesses', 'Wholesalers & distributors', 'Manufacturers', 'Seasonal inventory'],
        'facts' => [
            'Location' => 'Makeni, Lusaka',
            'Security' => '24/7',
            'Storage' => 'Climate-controlled',
            'Services' => 'Inventory management, pick & pack',
        ],
        'cta' => 'Get a warehousing quote',
    ],
    [
        'id' => 'bulk-cargo',
        'quote' => 'bulk-cargo',
        'mode' => 'Heavy',
        'icon' => 'fa-truck-ramp-box',
        'title' => 'Bulk & project cargo',
        'summary' => 'Large, heavy and industrial loads moved with the right equipment, planned around your project schedule.',
        'for' => ['Mining companies', 'Construction firms', 'Agricultural businesses', 'Industrial manufacturers'],
        'facts' => [
            'Flatbed trucks' => 'Up to 30 tons',
            'Container trucks' => '20ft & 40ft',
            'Refrigerated' => 'Cold chain',
            'Tankers' => 'Liquid transport',
        ],
        'cta' => 'Get a bulk cargo quote',
    ],
    [
        'id' => 'same-day',
        'quote' => 'same-day',
        'mode' => 'Express',
        'icon' => 'fa-bolt',
        'title' => 'Same-day delivery',
        'summary' => 'Urgent documents and parcels delivered across Lusaka within hours, not days.',
        'for' => ['Urgent business documents', 'E-commerce orders', 'Medical supplies', 'Legal firms'],
        'facts' => [
            'Delivery time' => '2–8 hours',
            'Coverage' => 'Within Lusaka city limits',
        ],
        'cta' => 'Book a same-day delivery',
    ],
];
@endphp
@extends('layouts.app')

@section('title', 'Freight Services: Road, Air, Sea, Customs & Warehousing - Forus Freight')
@section('meta_description', 'Forus Freight services: cross-border road freight, air and sea freight, customs clearance, warehousing in Lusaka, bulk and project cargo, same-day delivery and a free China shopping address.')

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Forus Freight services',
    'itemListElement' => collect($services)->values()->map(fn ($service, $index) => [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $service['title'],
        'url' => url('/services#'.$service['id']),
        'item' => [
            '@type' => 'Service',
            'name' => $service['title'],
            'serviceType' => $service['mode'],
            'description' => $service['summary'],
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => 'Forus Freight Limited',
                'url' => url('/'),
            ],
            'areaServed' => [
                '@type' => 'Place',
                'name' => 'Zambia and the SADC region',
            ],
        ],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        /* ---------- Hero ---------- */
        .s-hero {
            padding: 4rem 0 4.5rem;
            border-bottom: 1px solid var(--line);
        }

        .s-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 4rem;
            align-items: start;
        }

        .s-hero h1 {
            font-size: clamp(2.3rem, 4.4vw, 3.6rem);
            margin: 1.1rem 0 1.25rem;
        }

        .s-hero .h-lead {
            max-width: 32rem;
            margin-bottom: 2rem;
        }

        .s-hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem 1.5rem;
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

        /* Index of services, styled like a cargo manifest */
        .s-index {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 0.75rem 1.5rem;
        }

        .s-index-head {
            display: flex;
            justify-content: space-between;
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 0.75rem 0;
            border-bottom: 2px solid var(--ink);
        }

        .s-index ol {
            list-style: none;
        }

        .s-index a {
            display: grid;
            grid-template-columns: 2.25rem 1fr auto;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--line);
            color: var(--ink);
            text-decoration: none;
            transition: padding 0.2s ease, color 0.2s ease;
        }

        .s-index li:last-child a {
            border-bottom: none;
        }

        .s-index a:hover {
            padding-left: 0.4rem;
            color: var(--teal-deep);
        }

        .s-index-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--muted);
        }

        .s-index-name {
            font-weight: 700;
        }

        .s-index-mode {
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .s-index-mode i {
            color: var(--teal);
        }

        /* ---------- Service blocks ---------- */
        .s-list {
            padding-top: 2rem;
            padding-bottom: 1rem;
        }

        .s-service {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            gap: 2rem 4rem;
            padding: 4rem 0;
            border-bottom: 1px solid var(--line);
            scroll-margin-top: 110px;
        }

        .s-service:last-child {
            border-bottom: none;
        }

        .s-service-tag {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1.25rem;
        }

        .s-service-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--ink);
            color: #fff;
            font-size: 1.1rem;
        }

        .s-service-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .s-service h2 {
            font-family: var(--display);
            font-stretch: 108%;
            font-weight: 800;
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .s-service-summary {
            color: var(--ink-2);
            font-size: 1.08rem;
            max-width: 32rem;
            margin-bottom: 1.75rem;
        }

        .s-service-for h3,
        .s-facts h3 {
            font-family: var(--mono);
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.85rem;
        }

        .s-service-for ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem 1.5rem;
            margin-bottom: 2rem;
        }

        .s-service-for li {
            display: flex;
            gap: 0.6rem;
            color: var(--ink-2);
            font-size: 0.95rem;
            line-height: 1.45;
        }

        .s-service-for li::before {
            content: '';
            flex-shrink: 0;
            width: 6px;
            height: 6px;
            margin-top: 0.55rem;
            border-radius: 50%;
            background: var(--orange);
        }

        .s-facts {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.75rem;
            align-self: start;
        }

        .s-facts dl {
            display: grid;
            margin-bottom: 1.5rem;
        }

        .s-facts dl div {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1.5rem;
            padding: 0.85rem 0;
            border-bottom: 1px dashed var(--line-strong);
        }

        .s-facts dl div:first-child {
            border-top: 2px solid var(--ink);
        }

        .s-facts dt {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .s-facts dd {
            font-family: var(--mono);
            font-weight: 500;
            font-size: 0.92rem;
            text-align: right;
            color: var(--ink);
        }

        .s-facts .h-btn {
            width: 100%;
        }

        /* China shopping: highlighted as the consumer offer */
        .s-china {
            margin: 1rem 0 2rem;
            background: var(--ink);
            color: #fff;
            border-radius: 28px;
            padding: 3.5rem;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            gap: 2.5rem 4rem;
            align-items: center;
            scroll-margin-top: 110px;
        }

        .s-china .h-eyebrow {
            color: #5eead4;
        }

        .s-china h2 {
            font-family: var(--display);
            font-stretch: 108%;
            font-weight: 800;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin: 0.9rem 0 1rem;
        }

        .s-china p {
            color: #c4d1d5;
            max-width: 32rem;
        }

        .s-china-steps {
            list-style: none;
            counter-reset: step;
        }

        .s-china-steps li {
            display: grid;
            grid-template-columns: 2.5rem 1fr;
            padding: 0.9rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            color: #dfe8ea;
        }

        .s-china-steps li:last-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        .s-china-steps li::before {
            counter-increment: step;
            content: '0' counter(step);
            font-family: var(--mono);
            color: var(--orange);
            font-size: 0.85rem;
            padding-top: 0.1rem;
        }

        .s-china-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.5rem;
            align-items: center;
            margin-top: 1.75rem;
        }

        .s-china .h-textlink {
            color: #5eead4;
        }

        /* ---------- Help choosing ---------- */
        .s-help {
            padding: 1rem 0 5.5rem;
        }

        .s-help-card {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) auto;
            gap: 2rem;
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 2.25rem 2.5rem;
        }

        .s-help-card h2 {
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.01em;
            margin-bottom: 0.4rem;
        }

        .s-help-card p {
            color: var(--ink-2);
        }

        .s-help-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 1024px) {
            .s-hero-grid,
            .s-service,
            .s-china {
                grid-template-columns: 1fr;
            }

            .s-hero-grid {
                gap: 2.5rem;
            }

            .s-china {
                padding: 2.5rem;
            }

            .s-help-card {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .s-hero {
                padding: 2.5rem 0 3rem;
            }

            .s-index {
                padding: 0.5rem 1.1rem;
            }

            .s-index-mode span {
                display: none;
            }

            .s-service {
                padding: 3rem 0;
            }

            .s-service-for ul {
                grid-template-columns: 1fr;
            }

            .s-facts {
                padding: 1.35rem;
            }

            .s-china {
                padding: 2rem 1.35rem;
                border-radius: 20px;
            }

            .s-help-card {
                padding: 1.75rem 1.35rem;
            }

            .s-hero-actions .h-btn,
            .s-china-actions .h-btn,
            .s-help-actions,
            .s-help-actions .h-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Services', 'url' => null]]])
<div class="ui-page">

    {{-- ============ HERO + INDEX ============ --}}
    <section class="s-hero">
        <div class="h-wrap s-hero-grid">
            <div>
                <span class="h-eyebrow">{{ $sections['title'] ?? 'Our services' }}</span>
                <h1 class="h-display">Everything your cargo needs, from origin to Lusaka</h1>
                <p class="h-lead">Road, air and sea freight, customs clearance and storage — handled by one team, with tracking from the moment we receive your goods.</p>
                <div class="s-hero-actions">
                    <a href="{{ route('quote') }}" class="h-btn h-btn--primary">Get a free quote <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-textlink"><i class="fab fa-whatsapp" aria-hidden="true"></i> Not sure? Ask us on WhatsApp</a>
                </div>
            </div>

            <nav class="s-index" aria-labelledby="indexTitle">
                <div class="s-index-head"><span id="indexTitle">Services</span><span>Mode</span></div>
                <ol>
                    @foreach ($services as $i => $service)
                        <li>
                            <a href="#{{ $service['id'] }}">
                                <span class="s-index-num">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="s-index-name">{{ $service['title'] }}</span>
                                <span class="s-index-mode"><span>{{ $service['mode'] }}</span><i class="fas {{ $service['icon'] }}" aria-hidden="true"></i></span>
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="#china-shopping">
                            <span class="s-index-num">{{ sprintf('%02d', count($services) + 1) }}</span>
                            <span class="s-index-name">Shop from China</span>
                            <span class="s-index-mode"><span>Parcels</span><i class="fas fa-cart-shopping" aria-hidden="true"></i></span>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    {{-- ============ SERVICES ============ --}}
    <div class="h-wrap s-list">
        @foreach ($services as $i => $service)
            <section class="s-service" id="{{ $service['id'] }}" aria-labelledby="{{ $service['id'] }}-title">
                <div>
                    <div class="s-service-tag">
                        <span class="s-service-icon"><i class="fas {{ $service['icon'] }}" aria-hidden="true"></i></span>
                        <span class="s-service-num">{{ sprintf('%02d', $i + 1) }} &middot; {{ strtoupper($service['mode']) }}</span>
                    </div>
                    <h2 id="{{ $service['id'] }}-title">{{ $service['title'] }}</h2>
                    <p class="s-service-summary">{{ $service['summary'] }}</p>
                    <div class="s-service-for">
                        <h3>Who it's for</h3>
                        <ul>
                            @foreach ($service['for'] as $who)
                                <li>{{ $who }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="s-facts">
                    <h3>At a glance</h3>
                    <dl>
                        @foreach ($service['facts'] as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                        @endforeach
                    </dl>
                    <a href="{{ route('quote', ['service' => $service['quote']]) }}" class="h-btn h-btn--dark">{{ $service['cta'] }} <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </section>
        @endforeach

        {{-- China shopping is a consumer product, so it gets its own treatment --}}
        <section class="s-china" id="china-shopping" aria-labelledby="chinaTitle">
            <div>
                <span class="h-eyebrow">{{ sprintf('%02d', count($services) + 1) }} &middot; For online shoppers</span>
                <h2 id="chinaTitle">Shop from China, collect in Lusaka</h2>
                <p>Get a free personal address at our warehouse in Foshan, China. Buy from Alibaba, Amazon, Shein, Temu and more — we receive, combine and ship your parcels to our Makeni warehouse in Lusaka.</p>
                <div class="s-china-actions">
                    @guest
                        <a href="{{ route('register') }}" class="h-btn h-btn--primary">Get my free China address <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <a href="{{ route('home') }}#shop-from-china" class="h-textlink">See how it works</a>
                    @else
                        <a href="{{ route('client.getting-started') }}" class="h-btn h-btn--primary">View my China address <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    @endguest
                </div>
            </div>
            <ol class="s-china-steps">
                <li>Create a free account and verify your email and phone</li>
                <li>Use your China address as the delivery address at checkout</li>
                <li>We consolidate your parcels and ship them to Zambia</li>
                <li>Track every stage, then collect from Makeni, Lusaka</li>
            </ol>
        </section>
    </div>

    {{-- ============ HELP CHOOSING ============ --}}
    <section class="s-help" aria-labelledby="helpTitle">
        <div class="h-wrap">
            <div class="s-help-card">
                <div>
                    <h2 id="helpTitle">Not sure which service you need?</h2>
                    <p>Tell us what you're moving, where from and how soon. We'll recommend the best option and give you a clear price.</p>
                </div>
                <div class="s-help-actions">
                    <a href="{{ route('quote') }}" class="h-btn h-btn--primary">Get a free quote</a>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-btn h-btn--outline"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
