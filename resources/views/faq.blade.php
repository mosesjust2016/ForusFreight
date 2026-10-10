@php
$page = \App\Models\CmsPage::where('slug', 'faq')->first();
$sections = $page?->sections ?? [];
$footerSections = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
$whatsappLink = 'https://wa.me/' . ($footerSections['whatsapp_number'] ?? '260572788685') . '?text=' . urlencode('Hi Forus Freight, I have a question');
$email = $footerSections['contact_email'] ?? 'info@forusfreight.com';

// Grouped questions. Answers must stay in line with what the business has
// confirmed (Services page) — they also feed the FAQPage structured data.
$groups = [
    [
        'id' => 'shipping',
        'title' => 'Shipping & services',
        'icon' => 'fa-truck',
        'items' => [
            ['q' => 'How long does shipping take?', 'a' => 'It depends on the route and service. Road freight typically takes 3–5 days to neighbouring countries and 5–10 days across the SADC region. Air freight is available for urgent shipments and usually takes 1–3 days. Your quote includes a specific timeframe.'],
            ['q' => 'Where do you ship from?', 'a' => 'We move freight into Zambia from South Africa (by road via Durban), China (through our Foshan warehouse) and the UAE (by air and sea from Dubai), and deliver across Zambia’s major cities.'],
            ['q' => 'Do you handle customs clearance?', 'a' => 'Yes. We prepare the documentation and handle clearance, so your goods don’t sit waiting at the border or the port. You’ll see the “Customs Clearance” and “Customs Cleared” stages on your tracking page.'],
            ['q' => 'Do you offer same-day delivery?', 'a' => 'Yes, within Lusaka city limits. Urgent documents and parcels are typically delivered in 2–8 hours.'],
            ['q' => 'Do you offer warehousing?', 'a' => 'Yes. We store cargo at our Makeni warehouse in Lusaka, which is climate-controlled with 24/7 security. We also offer inventory management and pick & pack. Contact us for rates.'],
            ['q' => 'Can you move large or heavy cargo?', 'a' => 'Yes. Our bulk and project cargo service uses flatbed trucks (up to 30 tons), 20ft and 40ft container trucks, refrigerated trucks for cold-chain goods and tankers for liquids.'],
        ],
    ],
    [
        'id' => 'china',
        'title' => 'Shopping from China',
        'icon' => 'fa-cart-shopping',
        'items' => [
            ['q' => 'How does the free China address work?', 'a' => 'Create a free account and verify your email and phone. Your personal address at our Foshan warehouse then appears in your account. Use it as the delivery address when you buy from Chinese stores, and we receive your parcels, combine them and ship them to Lusaka.'],
            ['q' => 'Which stores can I buy from?', 'a' => 'Any store that delivers within China — for example Alibaba, Amazon, eBay, Shein and Temu.'],
            ['q' => 'How should my parcels be labelled?', 'a' => 'Make sure the shipping mark ZMFFL and your customer ID are on every parcel, together with your name and phone number. That’s how our warehouse matches your goods to your account. You’ll find your exact shipping mark in your account.'],
            ['q' => 'Where do I collect my parcels?', 'a' => 'From our Makeni warehouse in Lusaka once your shipment shows “Ready for Collection”. We can also arrange delivery.'],
            ['q' => 'Does it cost anything to sign up?', 'a' => 'No. The account and your China address are free. You only pay for shipping your goods to Zambia.'],
        ],
    ],
    [
        'id' => 'tracking',
        'title' => 'Tracking & updates',
        'icon' => 'fa-location-crosshairs',
        'items' => [
            ['q' => 'How do I track my shipment?', 'a' => 'Enter your tracking or serial number on our tracking page — no login needed. Customers can also log in to see all their shipments in one place.'],
            ['q' => 'Where do I find my tracking number?', 'a' => 'It’s in your shipment emails and on your invoice. It looks like DUR.37977 or 773421428627451.'],
            ['q' => 'Will I be notified when something changes?', 'a' => 'Yes. We email you every time your shipment’s status changes, and send a text or WhatsApp message when it’s delivered.'],
        ],
    ],
    [
        'id' => 'payments',
        'title' => 'Payments & policies',
        'icon' => 'fa-receipt',
        'items' => [
            ['q' => 'What payment methods do you accept?', 'a' => 'We accept bank transfer and mobile money. Contact us for full payment details for your shipment.'],
            ['q' => 'What insurance options are available?', 'a' => 'We offer insurance cover for shipments, with options for additional cover based on cargo value. Full details are included in your shipment quote.'],
            ['q' => 'How do I request a refund or raise a complaint?', 'a' => 'Contact us at ' . $email . ' or +260 572 788 685 with your booking or waybill reference. See our Refund Policy for when a refund or re-performance applies.'],
            ['q' => 'What are your business hours?', 'a' => 'Our offices are open Monday to Friday, 08:00–17:00 (CAT). You can send an enquiry any time and we’ll respond the next business day.'],
        ],
    ],
];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Frequently Asked Questions') . ' - Forus Freight')
@section('meta_description', 'Answers about Forus Freight shipping times, customs clearance, the free China shopping address, tracking, payments and insurance.')

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($groups)->flatMap(fn ($g) => $g['items'])->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        .f-hero {
            padding: 3.5rem 0 3rem;
            border-bottom: 1px solid var(--line);
        }

        .f-hero-inner {
            max-width: 760px;
        }

        .f-hero h1 {
            font-size: clamp(2.2rem, 4.2vw, 3.4rem);
            margin: 1rem 0 0.9rem;
        }

        .f-search {
            position: relative;
            margin-top: 2rem;
        }

        .f-search i {
            position: absolute;
            left: 1.15rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .f-search input {
            width: 100%;
            height: 58px;
            padding: 0 1.1rem 0 3rem;
            border: 2px solid var(--line-strong);
            border-radius: 14px;
            background: #fff;
            font-size: 1.05rem;
            color: var(--ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .f-search input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.15);
        }

        .f-body {
            padding: 4rem 0 5.5rem;
        }

        .f-grid {
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 4rem;
            align-items: start;
        }

        /* Category navigation */
        .f-nav {
            position: sticky;
            top: 130px;
        }

        .f-nav-label {
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.85rem;
        }

        .f-nav ul {
            list-style: none;
            border-top: 2px solid var(--ink);
        }

        .f-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--line);
            color: var(--ink-2);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease, padding 0.2s ease;
        }

        .f-nav a:hover,
        .f-nav a.is-current {
            color: var(--ink);
            padding-left: 0.35rem;
        }

        .f-nav a i {
            width: 1.1rem;
            text-align: center;
            color: var(--teal);
        }

        .f-nav a small {
            margin-left: auto;
            font-family: var(--mono);
            font-size: 0.75rem;
            color: var(--muted);
        }

        .f-nav-help {
            margin-top: 1.75rem;
            padding: 1.25rem;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            font-size: 0.92rem;
            color: var(--ink-2);
        }

        .f-nav-help a {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.6rem;
            color: var(--teal-deep);
            font-weight: 700;
            text-decoration: none;
        }

        /* Question groups */
        .f-group + .f-group {
            margin-top: 3.5rem;
        }

        .f-group {
            scroll-margin-top: 120px;
        }

        .f-group-title {
            display: flex;
            align-items: baseline;
            gap: 0.85rem;
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.01em;
            margin-bottom: 1rem;
        }

        .f-group-title span {
            font-family: var(--mono);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--orange);
        }

        .f-list {
            border-top: 1px solid var(--line-strong);
        }

        .f-list details {
            border-bottom: 1px solid var(--line-strong);
        }

        .f-list summary {
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .f-list summary::-webkit-details-marker {
            display: none;
        }

        .f-list summary::after {
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

        .f-list summary:hover::after {
            border-color: var(--ink);
        }

        .f-list details[open] summary::after {
            transform: rotate(45deg);
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .f-list details p {
            color: var(--ink-2);
            padding: 0 3rem 1.4rem 0;
            max-width: 46rem;
        }

        .f-list mark {
            background: #ffe2cc;
            color: inherit;
            border-radius: 3px;
            padding: 0 2px;
        }

        .f-empty {
            display: none;
            padding: 2rem;
            text-align: center;
            background: #fff;
            border: 1px dashed var(--line-strong);
            border-radius: var(--radius);
            color: var(--ink-2);
        }

        .f-empty a {
            color: var(--teal-deep);
            font-weight: 700;
        }

        .f-still {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1.5rem 2.5rem;
            align-items: center;
            background: var(--ink);
            color: #fff;
            border-radius: 24px;
            padding: 2.25rem 2.5rem;
        }

        .f-still h2 {
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.55rem;
            margin-bottom: 0.35rem;
        }

        .f-still p {
            color: #c4d1d5;
        }

        .f-still-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .f-still .h-btn--ghost {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            background: transparent;
        }

        .f-still .h-btn--ghost:hover {
            border-color: #fff;
        }

        @media (max-width: 900px) {
            .f-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .f-nav {
                position: static;
            }

            .f-nav ul {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                border-top: none;
            }

            .f-nav a {
                border: 1px solid var(--line-strong);
                border-radius: 999px;
                padding: 0.5rem 0.9rem;
                background: #fff;
                font-size: 0.9rem;
            }

            .f-nav a:hover,
            .f-nav a.is-current {
                padding-left: 0.9rem;
            }

            .f-nav a small,
            .f-nav-help,
            .f-nav-label {
                display: none;
            }

            .f-still {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .f-hero {
                padding: 2.25rem 0 2.25rem;
            }

            .f-body {
                padding: 2.5rem 0 4rem;
            }

            .f-list details p {
                padding-right: 0;
            }

            .f-still {
                padding: 1.75rem 1.35rem;
            }

            .f-still-actions,
            .f-still-actions .h-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'FAQ', 'url' => null]]])
<div class="ui-page">

    <section class="f-hero">
        <div class="h-wrap">
            <div class="f-hero-inner">
                <span class="h-eyebrow">{{ $sections['title'] ?? 'Frequently asked questions' }}</span>
                <h1 class="h-display">Questions, answered</h1>
                <p class="h-lead">Everything about shipping times, customs, shopping from China, tracking and payments. Can’t find it? Our Lusaka team is a WhatsApp message away.</p>
                <div class="f-search" role="search">
                    <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                    <label for="faqSearch" class="sr-only">Search the questions</label>
                    <input id="faqSearch" type="search" placeholder="Search, e.g. customs, China, insurance" autocomplete="off">
                </div>
            </div>
        </div>
    </section>

    <section class="f-body">
        <div class="h-wrap f-grid">
            <aside class="f-nav" aria-label="Question categories">
                <p class="f-nav-label">Topics</p>
                <ul>
                    @foreach ($groups as $group)
                        <li>
                            <a href="#{{ $group['id'] }}" data-group-link="{{ $group['id'] }}">
                                <i class="fas {{ $group['icon'] }}" aria-hidden="true"></i>{{ $group['title'] }}<small>{{ count($group['items']) }}</small>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="f-nav-help">
                    Still stuck?
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> Ask on WhatsApp</a>
                </div>
            </aside>

            <div>
                @foreach ($groups as $gi => $group)
                    <section class="f-group" id="{{ $group['id'] }}" aria-labelledby="{{ $group['id'] }}-title">
                        <h2 class="f-group-title" id="{{ $group['id'] }}-title"><span>{{ sprintf('%02d', $gi + 1) }}</span>{{ $group['title'] }}</h2>
                        <div class="f-list">
                            @foreach ($group['items'] as $item)
                                <details>
                                    <summary><span class="f-q">{{ $item['q'] }}</span></summary>
                                    <p>{{ $item['a'] }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <p class="f-empty" id="faqEmpty" role="status">No questions match your search. <a href="{{ $whatsappLink }}" target="_blank" rel="noopener">Ask us on WhatsApp</a> and we’ll answer it directly.</p>

                <div class="f-still">
                    <div>
                        <h2>Still have a question?</h2>
                        <p>Message our Lusaka team, or get a free quote and we’ll talk you through the details.</p>
                    </div>
                    <div class="f-still-actions">
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-btn h-btn--primary"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
                        <a href="{{ route('contact') }}" class="h-btn h-btn--ghost">Contact page</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script>
    (function () {
        const input = document.getElementById('faqSearch');
        const groups = Array.from(document.querySelectorAll('.f-group'));
        const empty = document.getElementById('faqEmpty');
        const items = Array.from(document.querySelectorAll('.f-list details')).map(d => ({
            el: d,
            summary: d.querySelector('summary .f-q'),
            answer: d.querySelector('p'),
            q: d.querySelector('summary .f-q').textContent,
            a: d.querySelector('p').textContent,
        }));

        const escapeHtml = s => s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const highlight = (text, term) => {
            if (!term) return escapeHtml(text);
            const re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            return escapeHtml(text).replace(re, '<mark>$1</mark>');
        };

        // Filter questions as the visitor types; matching answers open automatically
        input.addEventListener('input', () => {
            const term = input.value.trim().toLowerCase();
            let shown = 0;
            items.forEach(it => {
                const match = !term || (it.q + ' ' + it.a).toLowerCase().includes(term);
                it.el.hidden = !match;
                it.el.open = !!term && match;
                it.summary.innerHTML = highlight(it.q, term);
                it.answer.innerHTML = highlight(it.a, term);
                if (match) shown++;
            });
            groups.forEach(g => { g.hidden = !g.querySelector('details:not([hidden])'); });
            empty.style.display = shown ? 'none' : 'block';
        });

        // Open a question linked directly, e.g. /faq#china
        if (location.hash) {
            const target = document.querySelector(location.hash);
            if (target && target.classList.contains('f-group')) {
                const first = target.querySelector('details');
                if (first) first.open = true;
            }
        }

        // Highlight the topic currently in view
        const links = Array.from(document.querySelectorAll('[data-group-link]'));
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        links.forEach(l => l.classList.toggle('is-current', l.dataset.groupLink === e.target.id));
                    }
                });
            }, { rootMargin: '-40% 0px -55% 0px' });
            groups.forEach(g => io.observe(g));
        }
    })();
</script>
@endsection
