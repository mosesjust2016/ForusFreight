@php
$page = \App\Models\CmsPage::where('slug', 'contact')->first();
$sections = $page?->sections ?? [];
// Contact details come from the footer settings so every page shows the same
// numbers and addresses (the contact page's own CMS fields held placeholders).
$footer = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];

$phones = collect(!empty($footer['contact_phones']) ? $footer['contact_phones'] : [['number' => '+260 572 788 685'], ['number' => '+260 766 193059']])
    ->pluck('number')->filter()->values();
$email = $footer['contact_email'] ?? 'info@forusfreight.com';
$whatsappNumber = $footer['whatsapp_number'] ?? '260572788685';
$whatsappLink = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I have an enquiry');
$office = $footer['contact_address'] ?? 'Metrolux Plaza, Plot No. 401A/8 Kafue Road';
$officeCity = $footer['contact_city'] ?? 'Lusaka, Zambia';
$warehouse = $footer['contact_warehouse_address'] ?? 'Plot No. F/26/397a, Robert ‘B’ Warehouse ‘A’, Off Kafue Road, Makeni';
$warehouseCity = $footer['contact_warehouse_city'] ?? 'Lusaka, Zambia';
$hours = $sections['hours'] ?? 'Mon–Fri, 08:00–17:00';
$mapsLink = fn (string $address) => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
// "+260 766 193059" and "260766193059" both display as "+260 766 193 059"
$formatPhone = function (string $number): string {
    $digits = preg_replace('/\D/', '', $number);
    return preg_match('/^(260)(\d{3})(\d{3})(\d{3})$/', $digits, $m) ? "+{$m[1]} {$m[2]} {$m[3]} {$m[4]}" : $number;
};
// Break long addresses at natural points ("…Warehouse 'A'" / "Off Kafue Road, Makeni")
$addressLines = fn (string $address) => preg_split('/,\s+(?=Off\b|Plot\b)/i', $address);
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Contact Us') . ' - Forus Freight')
@section('meta_description', 'Contact Forus Freight in Lusaka, Zambia: call, WhatsApp or email our team, or visit our Kafue Road office and Makeni warehouse.')

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => 'Contact Forus Freight',
    'url' => url('/contact'),
    'mainEntity' => [
        '@type' => 'LocalBusiness',
        'name' => 'Forus Freight Limited',
        'telephone' => $phones->first(),
        'email' => $email,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $office,
            'addressLocality' => 'Lusaka',
            'addressCountry' => 'ZM',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        .c-hero {
            padding: 3.5rem 0 3rem;
        }

        .c-hero-inner {
            max-width: 760px;
        }

        .c-hero h1 {
            font-size: clamp(2.2rem, 4.2vw, 3.4rem);
            margin: 1rem 0 0.9rem;
        }

        /* Quick channels */
        .c-channels {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 2.5rem;
        }

        .c-channel {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            padding: 1.5rem;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            color: var(--ink);
            text-decoration: none;
            transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        a.c-channel:hover {
            border-color: var(--ink);
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(13, 27, 32, 0.08);
        }

        .c-channel-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: var(--ink);
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .c-channel--whatsapp .c-channel-icon {
            background: #25d366;
            color: #06381c;
        }

        .c-channel-text {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 0;
        }

        .c-channel-label {
            font-family: var(--mono);
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .c-channel strong {
            font-size: 1.15rem;
            font-weight: 700;
            word-break: break-word;
        }

        .c-channel strong a {
            color: inherit;
            text-decoration: none;
        }

        .c-channel strong a:hover {
            text-decoration: underline;
        }

        .c-channel small {
            color: var(--muted);
            font-size: 0.9rem;
        }

        /* Form + details */
        .c-main {
            padding: 1.5rem 0 5.5rem;
        }

        .c-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 2rem;
            align-items: start;
        }

        .c-form-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 2.5rem;
        }

        .c-form-card h2 {
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.75rem;
            letter-spacing: -0.01em;
        }

        .c-form-intro {
            color: var(--ink-2);
            margin: 0.4rem 0 1.75rem;
        }

        .c-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .c-field {
            margin-bottom: 1.25rem;
        }

        .c-field label {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.45rem;
        }

        .c-field input,
        .c-field textarea {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid var(--line-strong);
            border-radius: 12px;
            background: var(--paper);
            font: inherit;
            font-size: 1rem;
            color: var(--ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .c-field input {
            min-height: 52px;
        }

        .c-field textarea {
            resize: vertical;
            min-height: 140px;
        }

        .c-field input:focus,
        .c-field textarea:focus {
            outline: none;
            background: #fff;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.15);
        }

        .c-field [aria-invalid="true"] {
            border-color: #dc2626;
        }

        .c-error {
            color: #b91c1c;
            font-size: 0.875rem;
            margin-top: 0.4rem;
        }

        .c-required {
            display: block;
            margin-top: 0.3rem;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .c-consent {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin: 0.25rem 0 1.5rem;
            font-size: 0.9rem;
            color: var(--ink-2);
            line-height: 1.5;
        }

        .c-consent input {
            width: 20px;
            height: 20px;
            margin-top: 0.1rem;
            flex-shrink: 0;
            accent-color: var(--teal);
        }

        .c-consent a {
            color: var(--teal-deep);
            font-weight: 600;
        }

        .c-summary {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }

        .c-submit {
            width: 100%;
            min-height: 56px;
            font-size: 1.05rem;
        }

        .c-submit:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .c-side {
            display: grid;
            gap: 1rem;
        }

        .c-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.6rem 1.75rem;
        }

        .c-card h3 {
            font-family: var(--mono);
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            padding-bottom: 0.8rem;
            border-bottom: 2px solid var(--ink);
            margin-bottom: 0.25rem;
        }

        .c-place {
            padding: 1rem 0;
            border-bottom: 1px dashed var(--line-strong);
        }

        .c-place:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .c-place strong {
            display: block;
            margin-bottom: 0.2rem;
        }

        .c-place address {
            font-style: normal;
            color: var(--ink-2);
            line-height: 1.55;
        }

        .c-place a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--teal-deep);
            text-decoration: none;
            min-height: 32px;
        }

        .c-place a:hover {
            text-decoration: underline;
        }

        .c-hours {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 1rem;
            font-weight: 600;
        }

        .c-hours i {
            color: var(--teal);
        }

        .c-quick {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .c-quick h3 {
            color: #8fa6ad;
            border-bottom-color: rgba(255, 255, 255, 0.3);
        }

        .c-quick a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.95rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }

        .c-quick a:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .c-quick a small {
            display: block;
            font-weight: 400;
            color: #b5c4c9;
            font-size: 0.88rem;
        }

        .c-quick a i {
            color: var(--orange);
            transition: transform 0.2s ease;
        }

        .c-quick a:hover i {
            transform: translateX(3px);
        }

        @media (max-width: 960px) {
            .c-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .c-hero {
                padding: 2.25rem 0 2rem;
            }

            .c-channels {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .c-channel {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                column-gap: 1rem;
                padding: 1.1rem 1.25rem;
            }

            .c-channel-icon {
                margin-bottom: 0;
            }

            .c-channel-text {
                flex: 1;
            }

            .c-form-card {
                padding: 1.5rem 1.25rem;
                border-radius: 18px;
            }

            .c-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Contact', 'url' => null]]])
<div class="ui-page">

    <section class="c-hero">
        <div class="h-wrap">
            <div class="c-hero-inner">
                <span class="h-eyebrow">{{ $sections['title'] ?? 'Contact us' }}</span>
                <h1 class="h-display">Talk to our Lusaka team</h1>
                <p class="h-lead">Questions about a shipment, a quote or shopping from China? Message us on WhatsApp, give us a call, or send a message below.</p>
            </div>

            <div class="c-channels">
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="c-channel c-channel--whatsapp">
                    <span class="c-channel-icon"><i class="fab fa-whatsapp" aria-hidden="true"></i></span>
                    <span class="c-channel-text">
                        <span class="c-channel-label">WhatsApp</span>
                        <strong>Chat with us</strong>
                        <small>{{ $formatPhone($whatsappNumber) }}</small>
                    </span>
                </a>
                <div class="c-channel">
                    <span class="c-channel-icon"><i class="fas fa-phone" aria-hidden="true"></i></span>
                    <span class="c-channel-text">
                        <span class="c-channel-label">Call</span>
                        @foreach ($phones as $phone)
                            <strong><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $formatPhone($phone) }}</a></strong>
                        @endforeach
                        <small>{{ $hours }}</small>
                    </span>
                </div>
                <a href="mailto:{{ $email }}" class="c-channel">
                    <span class="c-channel-icon"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                    <span class="c-channel-text">
                        <span class="c-channel-label">Email</span>
                        <strong>{{ $email }}</strong>
                        <small>For documents and detailed enquiries</small>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <section class="c-main">
        <div class="h-wrap c-grid">
            <div class="c-form-card">
                <h2 id="formTitle">{{ \Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::lower($sections['form_title'] ?? 'Send us a message')) }}</h2>
                <p class="c-form-intro">Our team replies by email or phone during business hours.<small class="c-required">Fields marked * are required.</small></p>

                <form id="contactForm" method="POST" action="{{ route('contact.submit') }}" novalidate aria-labelledby="formTitle">
                    @csrf
                    <div id="contactSummary" class="c-summary" role="alert" tabindex="-1" hidden></div>

                    <div class="c-row">
                        <div class="c-field">
                            <label for="contact_full_name">Full name *</label>
                            <input id="contact_full_name" type="text" name="full_name" autocomplete="name" required>
                        </div>
                        <div class="c-field">
                            <label for="contact_phone">Phone number *</label>
                            <input id="contact_phone" type="tel" name="phone" autocomplete="tel" placeholder="+260 …" required>
                        </div>
                    </div>
                    <div class="c-field">
                        <label for="contact_email">Email address *</label>
                        <input id="contact_email" type="email" name="email" autocomplete="email" required>
                    </div>
                    <div class="c-field">
                        <label for="contact_message">How can we help? *</label>
                        <textarea id="contact_message" name="message" rows="5" required placeholder="Include your tracking number if your message is about a shipment."></textarea>
                    </div>
                    <div class="c-consent">
                        <input type="checkbox" id="contact_consent" name="consent" value="1" required>
                        <label for="contact_consent">I agree to Forus Freight processing these details to respond to my message, in line with the <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</label>
                    </div>
                    <button type="submit" class="h-btn h-btn--primary c-submit">Send message <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                </form>
            </div>

            <aside class="c-side">
                <div class="c-card">
                    <h3>Visit us</h3>
                    <div class="c-place">
                        <strong>Head office</strong>
                        <address>@foreach ($addressLines($office) as $line){{ $line }}<br>@endforeach{{ $officeCity }}</address>
                        <a href="{{ $mapsLink($office . ', ' . $officeCity) }}" target="_blank" rel="noopener"><i class="fas fa-map-location-dot" aria-hidden="true"></i> Open in Google Maps</a>
                    </div>
                    <div class="c-place">
                        <strong>Warehouse &amp; collection point</strong>
                        <address>@foreach ($addressLines($warehouse) as $line){{ $line }}<br>@endforeach{{ $warehouseCity }}</address>
                        <a href="{{ $mapsLink($warehouse . ', ' . $warehouseCity) }}" target="_blank" rel="noopener"><i class="fas fa-map-location-dot" aria-hidden="true"></i> Open in Google Maps</a>
                    </div>
                    <p class="c-hours"><i class="far fa-clock" aria-hidden="true"></i> {{ $hours }}</p>
                </div>

                <div class="c-card c-quick">
                    <h3>Looking for something specific?</h3>
                    <a href="{{ route('quote') }}"><span>Get a free quote<small>Price and transit time for your cargo</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ route('tracking') }}"><span>Track a shipment<small>No login needed</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ route('faq') }}"><span>Read the FAQ<small>Shipping times, customs, payments</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </aside>
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script>
    (function () {
        const form = document.getElementById('contactForm');
        if (!form) return;
        const summary = document.getElementById('contactSummary');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        const messages = {
            full_name: 'Please enter your name.',
            phone: 'Please enter a phone number we can reach you on.',
            email: 'Please enter a valid email address, e.g. name@company.com.',
            message: 'Please tell us how we can help.',
            consent: 'Please tick this box so we can reply to you.',
        };

        function clearErrors() {
            summary.hidden = true;
            summary.textContent = '';
            form.querySelectorAll('.c-error').forEach(el => el.remove());
            form.querySelectorAll('[aria-invalid]').forEach(el => {
                el.removeAttribute('aria-invalid');
                el.removeAttribute('aria-describedby');
            });
        }

        function fieldError(field, text) {
            const id = field.id + '-error';
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', id);
            const p = document.createElement('p');
            p.className = 'c-error';
            p.id = id;
            p.textContent = text;
            (field.type === 'checkbox' ? field.closest('.c-consent') : field).insertAdjacentElement('afterend', p);
        }

        function showSummary(text, focusEl) {
            summary.textContent = text;
            summary.hidden = false;
            (focusEl || summary).focus();
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearErrors();

            // Check required fields on the page, next to each field
            let first = null, count = 0;
            form.querySelectorAll('[required]').forEach(field => {
                const empty = field.type === 'checkbox' ? !field.checked : !field.value.trim();
                const badEmail = field.type === 'email' && field.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim());
                if (empty || badEmail) {
                    fieldError(field, messages[field.name] || 'Please complete this field.');
                    first = first || field;
                    count++;
                }
            });
            if (count) {
                showSummary(count === 1 ? 'Please fix 1 field below.' : 'Please fix the ' + count + ' fields below.', first);
                return;
            }

            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Sending…';
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (response.status === 422 && data.errors) {
                    let firstField = null;
                    Object.entries(data.errors).forEach(([name, errs]) => {
                        const field = form.querySelector('[name="' + name + '"]');
                        if (field) {
                            fieldError(field, errs[0]);
                            firstField = firstField || field;
                        }
                    });
                    showSummary('Please check the highlighted fields.', firstField);
                    return;
                }
                if (!response.ok || data.success === false) {
                    throw new Error(data.message || 'send failed');
                }
                form.reset();
                window.location.href = '{{ route('thank-you') }}?type=contact';
            })
            .catch(() => {
                showSummary('Sorry, we couldn’t send your message just now. Please try again, or message us on WhatsApp.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            });
        });
    })();
</script>
@endsection
