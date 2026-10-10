@php
$page = \App\Models\CmsPage::where('slug', 'about')->first();
$sections = $page?->sections ?? [];
$footerSections = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
$whatsappLink = 'https://wa.me/' . ($footerSections['whatsapp_number'] ?? '260572788685') . '?text=' . urlencode('Hi Forus Freight, I would like to know more about your services');

$zambiaCities = [
    ['name' => 'Lusaka', 'note' => 'Head office & Makeni warehouse'],
    ['name' => 'Ndola & Kitwe', 'note' => 'Copperbelt'],
    ['name' => 'Livingstone', 'note' => 'Southern Province'],
    ['name' => 'Chipata', 'note' => 'Eastern Province'],
    ['name' => 'Solwezi', 'note' => 'North-Western Province'],
];

$international = [
    ['code' => 'DUR', 'name' => 'South Africa', 'note' => 'Road freight via Durban'],
    ['code' => 'FUO', 'name' => 'China', 'note' => 'Foshan warehouse & consolidation'],
    ['code' => 'DXB', 'name' => 'UAE', 'note' => 'Air & sea freight from Dubai'],
];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'About Us') . ' - Forus Freight')
@section('meta_description', 'Forus Freight Limited is a Lusaka-based freight forwarder trusted by hundreds of businesses — road, air and sea freight, customs clearance and warehousing between Zambia, South Africa, China and the UAE.')

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        /* ---------- Hero ---------- */
        .a-hero {
            padding: 4rem 0 4.5rem;
            border-bottom: 1px solid var(--line);
        }

        .a-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 4rem;
            align-items: center;
        }

        .a-hero h1 {
            font-size: clamp(2.3rem, 4.4vw, 3.6rem);
            margin: 1.1rem 0 1.25rem;
        }

        .a-hero .h-lead {
            max-width: 34rem;
        }

        /* Company file: the key facts, set like a shipping document */
        .a-file {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.75rem;
            box-shadow: 0 24px 50px rgba(13, 27, 32, 0.08);
        }

        .a-file-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.25rem 1rem;
            padding-bottom: 0.85rem;
            border-bottom: 2px solid var(--ink);
        }

        .a-file-head strong {
            font-family: var(--display);
            font-stretch: 112%;
            font-weight: 800;
            font-size: 1.05rem;
        }

        .a-file-head span {
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .a-file dl div {
            display: grid;
            grid-template-columns: 8.5rem 1fr;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px dashed var(--line-strong);
        }

        .a-file dl div:last-child {
            border-bottom: none;
        }

        .a-file dt {
            font-family: var(--mono);
            font-size: 0.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            padding-top: 0.15rem;
        }

        .a-file dd {
            font-weight: 600;
            line-height: 1.45;
        }

        /* ---------- Story ---------- */
        .a-section {
            padding: 6rem 0;
        }

        .a-two {
            display: grid;
            grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.2fr);
            gap: 2rem 4rem;
            align-items: start;
        }

        .a-two .h-eyebrow {
            margin-bottom: 1rem;
        }

        .a-prose {
            font-size: 1.12rem;
            color: var(--ink-2);
            line-height: 1.7;
        }

        .a-prose p + p {
            margin-top: 1.1rem;
        }

        .a-prose p:first-child {
            font-size: 1.3rem;
            line-height: 1.55;
            color: var(--ink);
        }

        .a-prose h2,
        .a-prose h3 {
            font-family: var(--display);
            font-weight: 800;
            color: var(--ink);
            margin: 1.5rem 0 0.75rem;
        }

        /* ---------- Mission & vision ---------- */
        .a-mv {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1px;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 28px;
            overflow: hidden;
        }

        .a-mv-item {
            background: var(--ink);
            color: #fff;
            padding: 3rem;
        }

        .a-mv-item .h-eyebrow {
            color: #5eead4;
            margin-bottom: 1.25rem;
        }

        .a-mv-item blockquote {
            font-family: var(--display);
            font-stretch: 104%;
            font-weight: 700;
            font-size: clamp(1.35rem, 2.2vw, 1.75rem);
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        /* ---------- How we work ---------- */
        .a-principles {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            border-top: 2px solid var(--ink);
        }

        .a-principles li {
            padding: 2rem 2rem 0 0;
        }

        .a-principles li + li {
            padding-left: 2rem;
            border-left: 1px solid var(--line);
        }

        .a-principle-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--orange);
        }

        .a-principles h3 {
            font-family: var(--display);
            font-stretch: 105%;
            font-weight: 700;
            font-size: 1.35rem;
            margin: 0.5rem 0 0.6rem;
        }

        .a-principles p {
            color: var(--ink-2);
        }

        /* ---------- Where we operate ---------- */
        .a-reach {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .a-reach-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.75rem;
        }

        .a-reach-card h3 {
            font-family: var(--mono);
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            padding-bottom: 0.85rem;
            border-bottom: 2px solid var(--ink);
        }

        .a-reach-card ul {
            list-style: none;
        }

        .a-reach-card li {
            display: grid;
            grid-template-columns: 3.25rem 1fr;
            gap: 0.75rem;
            align-items: baseline;
            padding: 0.9rem 0;
            border-bottom: 1px dashed var(--line-strong);
        }

        .a-reach-card li:last-child {
            border-bottom: none;
        }

        .a-reach-code {
            font-family: var(--mono);
            font-size: 0.78rem;
            color: var(--teal-deep);
        }

        .a-reach-card strong {
            display: block;
            font-weight: 700;
        }

        .a-reach-card small {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .a-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.5rem;
            align-items: center;
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

        @media (max-width: 1024px) {
            .a-hero-grid,
            .a-two {
                grid-template-columns: 1fr;
            }

            .a-hero-grid {
                gap: 2.5rem;
            }

            .a-principles {
                grid-template-columns: 1fr;
                border-top: none;
            }

            .a-principles li,
            .a-principles li + li {
                padding: 1.5rem 0;
                border-left: none;
                border-top: 2px solid var(--ink);
            }
        }

        @media (max-width: 720px) {
            .a-hero {
                padding: 2.5rem 0 3rem;
            }

            .a-section {
                padding: 4rem 0;
            }

            .a-file {
                padding: 1.25rem;
            }

            .a-file dl div {
                grid-template-columns: 1fr;
                gap: 0.15rem;
            }

            .a-mv,
            .a-reach {
                grid-template-columns: 1fr;
            }

            .a-mv {
                border-radius: 20px;
            }

            .a-mv-item {
                padding: 2rem 1.5rem;
            }

            .a-actions .h-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'About', 'url' => null]]])
<div class="ui-page">

    {{-- ============ HERO ============ --}}
    <section class="a-hero">
        <div class="h-wrap a-hero-grid">
            <div>
                <span class="h-eyebrow">{{ $sections['title'] ?? 'About us' }}</span>
                <h1 class="h-display">A Lusaka team that makes importing simple</h1>
                <p class="h-lead">Forus Freight Limited is a freight forwarder trusted by hundreds of businesses — and a growing number of online shoppers — to move goods into Zambia from South Africa, China and the UAE.</p>
            </div>

            <aside class="a-file" aria-labelledby="fileTitle">
                <div class="a-file-head">
                    <strong id="fileTitle">FORUS FREIGHT LTD</strong>
                    <span>Company file</span>
                </div>
                <dl>
                    <div><dt>Head office</dt><dd>{{ $footerSections['contact_city'] ?? 'Lusaka, Zambia' }}</dd></div>
                    <div><dt>Warehouses</dt><dd>Makeni, Lusaka &middot; Foshan, China</dd></div>
                    <div><dt>Routes</dt><dd>South Africa, China &amp; UAE &rarr; Zambia</dd></div>
                    <div><dt>Services</dt><dd>Road, air &amp; sea freight, customs, warehousing</dd></div>
                    <div><dt>Customers</dt><dd>Hundreds of businesses &amp; online shoppers</dd></div>
                    <div><dt>Open</dt><dd>Mon–Fri, 08:00–17:00</dd></div>
                </dl>
            </aside>
        </div>
    </section>

    {{-- ============ STORY ============ --}}
    <section class="a-section" aria-labelledby="storyTitle">
        <div class="h-wrap a-two">
            <div>
                <span class="h-eyebrow">Our story</span>
                <h2 class="h-h2" id="storyTitle">Built to take the hassle out of cross-border freight</h2>
            </div>
            <div class="a-prose">
                {!! $sections['content'] ?? '<p>Forus Freight was founded to simplify cross-border logistics between Zambia and South Africa. Since then, we have grown into a trusted partner for hundreds of businesses.</p><p>Our team handles everything from documentation to delivery, so your cargo reaches its destination safely — and you always know where it is.</p>' !!}
            </div>
        </div>
    </section>

    {{-- ============ MISSION & VISION ============ --}}
    <section class="a-section" style="padding-top: 0;" aria-label="Mission and vision">
        <div class="h-wrap">
            <div class="a-mv">
                <div class="a-mv-item">
                    <span class="h-eyebrow">Our mission</span>
                    <blockquote>{{ $sections['mission'] ?? 'To deliver reliable, efficient and cost-effective logistics that help businesses grow across borders.' }}</blockquote>
                </div>
                <div class="a-mv-item">
                    <span class="h-eyebrow">Our vision</span>
                    <blockquote>{{ $sections['vision'] ?? 'To become the leading cross-border logistics provider in Southern Africa, recognised for excellence and innovation.' }}</blockquote>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ HOW WE WORK ============ --}}
    <section class="a-section" style="padding-top: 0;" aria-labelledby="workTitle">
        <div class="h-wrap">
            <div class="a-two" style="margin-bottom: 3rem;">
                <div>
                    <span class="h-eyebrow">How we work</span>
                    <h2 class="h-h2" id="workTitle">What you can expect from us</h2>
                </div>
                <p class="h-lead" style="align-self: end;">Moving freight across borders involves a lot of moving parts. Our job is to make it feel simple on your side.</p>
            </div>
            <ol class="a-principles">
                <li>
                    <span class="a-principle-num">01</span>
                    <h3>One team, start to finish</h3>
                    <p>Pickup, paperwork, customs and delivery are handled by the same team — you don't have to coordinate anyone.</p>
                </li>
                <li>
                    <span class="a-principle-num">02</span>
                    <h3>A clear price up front</h3>
                    <p>Every shipment starts with a free quote, so you know the cost and the expected transit time before anything moves.</p>
                </li>
                <li>
                    <span class="a-principle-num">03</span>
                    <h3>You always know where it is</h3>
                    <p>Track any shipment online, get an email at every status change, and reach a real person on WhatsApp.</p>
                </li>
            </ol>
        </div>
    </section>

    {{-- ============ WHERE WE OPERATE ============ --}}
    <section class="a-section" style="padding-top: 0;" aria-labelledby="reachTitle">
        <div class="h-wrap">
            <div class="a-two" style="margin-bottom: 3rem;">
                <div>
                    <span class="h-eyebrow">Where we operate</span>
                    <h2 class="h-h2" id="reachTitle">Across Zambia, and the routes that feed it</h2>
                </div>
                <p class="h-lead" style="align-self: end;">We deliver across Zambia's major cities and move freight in from our key international routes.</p>
            </div>
            <div class="a-reach">
                <div class="a-reach-card">
                    <h3>Within Zambia</h3>
                    <ul>
                        @foreach ($zambiaCities as $i => $city)
                            <li>
                                <span class="a-reach-code">{{ sprintf('%02d', $i + 1) }}</span>
                                <span><strong>{{ $city['name'] }}</strong><small>{{ $city['note'] }}</small></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="a-reach-card">
                    <h3>International routes</h3>
                    <ul>
                        @foreach ($international as $route)
                            <li>
                                <span class="a-reach-code">{{ $route['code'] }}</span>
                                <span><strong>{{ $route['name'] }}</strong><small>{{ $route['note'] }}</small></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="a-actions">
                <a href="{{ route('services') }}" class="h-btn h-btn--dark">Explore our services <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-textlink"><i class="fab fa-whatsapp" aria-hidden="true"></i> Talk to our team</a>
            </div>
        </div>
    </section>

</div>
@endsection
