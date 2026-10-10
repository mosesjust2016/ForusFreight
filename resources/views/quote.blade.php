@php
$footer = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first()?->sections ?? [];
$whatsappLink = 'https://wa.me/' . ($footer['whatsapp_number'] ?? '260572788685') . '?text=' . urlencode('Hi Forus Freight, I need a quote for logistics services');
@endphp
@extends('layouts.app')

@section('title', 'Get a Free Freight Quote - Forus Freight')
@section('meta_description', 'Request a free, no-obligation freight quote from Forus Freight: road, air and sea freight, customs clearance, warehousing, bulk cargo and same-day delivery in Lusaka.')

@section('styles')
    @include('partials.ui-base-styles')
    <style>
        .q-hero {
            padding: 3.25rem 0 2.5rem;
        }

        .q-hero-inner {
            max-width: 760px;
        }

        .q-hero h1 {
            font-size: clamp(2.2rem, 4.2vw, 3.4rem);
            margin: 1rem 0 0.9rem;
        }

        .q-ticks {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem 1.75rem;
            margin-top: 1.5rem;
            color: var(--ink-2);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .q-ticks i {
            color: var(--teal);
            margin-right: 0.45rem;
        }

        .q-main {
            padding: 0.5rem 0 5.5rem;
        }

        .q-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 0.75fr);
            gap: 2rem;
            align-items: start;
        }

        /* Form card */
        .q-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 2.5rem;
        }

        .q-step {
            border: 0;
            margin: 0;
            padding: 0;
            min-width: 0;
        }

        .q-step + .q-step {
            margin-top: 2.25rem;
            padding-top: 2rem;
            border-top: 1px dashed var(--line-strong);
        }

        .q-step-head {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .q-step-num {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--orange);
        }

        .q-step-head h2 {
            font-family: var(--display);
            font-stretch: 106%;
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.01em;
        }

        .q-step-head small {
            margin-left: auto;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .q-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 1.25rem;
        }

        .q-field {
            margin-bottom: 1.15rem;
        }

        .q-field label {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.45rem;
        }

        .q-field label .q-opt {
            font-weight: 400;
            color: var(--muted);
        }

        .q-field input,
        .q-field select,
        .q-field textarea {
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

        .q-field input,
        .q-field select {
            min-height: 52px;
        }

        .q-field textarea {
            resize: vertical;
            min-height: 110px;
        }

        .q-field input:focus,
        .q-field select:focus,
        .q-field textarea:focus {
            outline: none;
            background: #fff;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.15);
        }

        .q-field [aria-invalid="true"] {
            border-color: #dc2626;
        }

        .quote-field-error {
            color: #b91c1c;
            font-size: 0.875rem;
            margin-top: 0.4rem;
        }

        .quote-error-summary {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .q-consent {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin: 0.5rem 0 1.5rem;
            font-size: 0.9rem;
            color: var(--ink-2);
            line-height: 1.5;
        }

        .q-consent input {
            width: 20px;
            height: 20px;
            margin-top: 0.1rem;
            flex-shrink: 0;
            accent-color: var(--teal);
        }

        .q-consent a {
            color: var(--teal-deep);
            font-weight: 600;
        }

        .q-submit {
            width: 100%;
            min-height: 58px;
            font-size: 1.08rem;
        }

        .q-submit:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .q-fine {
            margin-top: 0.85rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* Side panel */
        .q-side {
            position: sticky;
            top: 125px;
            display: grid;
            gap: 1rem;
        }

        .q-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.6rem 1.75rem;
        }

        .q-box h3 {
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

        .q-next {
            list-style: none;
            counter-reset: next;
        }

        .q-next li {
            display: grid;
            grid-template-columns: 2.25rem 1fr;
            padding: 0.9rem 0;
            border-bottom: 1px dashed var(--line-strong);
            color: var(--ink-2);
            font-size: 0.95rem;
        }

        .q-next li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .q-next li::before {
            counter-increment: next;
            content: '0' counter(next);
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--orange);
            padding-top: 0.15rem;
        }

        .q-next strong {
            display: block;
            color: var(--ink);
        }

        .q-talk {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .q-talk h3 {
            color: #8fa6ad;
            border-bottom-color: rgba(255, 255, 255, 0.3);
        }

        .q-talk p {
            color: #c4d1d5;
            margin: 0.85rem 0 1.1rem;
            font-size: 0.95rem;
        }

        .q-talk-actions {
            display: grid;
            gap: 0.6rem;
        }

        .q-talk .h-btn {
            width: 100%;
        }

        .q-talk .h-btn--ghost {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            background: transparent;
        }

        .q-talk .h-btn--ghost:hover {
            border-color: #fff;
        }

        .q-china {
            display: flex;
            gap: 0.9rem;
            align-items: flex-start;
            background: #fff6ef;
            border: 1px solid #ffd9bf;
            border-radius: var(--radius);
            padding: 1.25rem 1.4rem;
            font-size: 0.93rem;
            color: var(--ink-2);
        }

        .q-china i {
            color: var(--orange);
            font-size: 1.2rem;
            margin-top: 0.15rem;
        }

        .q-china strong {
            color: var(--ink);
        }

        .q-china a {
            display: inline-block;
            margin-top: 0.35rem;
            color: #b54400;
            font-weight: 700;
            text-decoration: none;
        }

        .q-china a:hover {
            text-decoration: underline;
        }

        @media (max-width: 1000px) {
            .q-grid {
                grid-template-columns: 1fr;
            }

            .q-side {
                position: static;
            }
        }

        @media (max-width: 640px) {
            .q-hero {
                padding: 2.25rem 0 1.75rem;
            }

            .q-card {
                padding: 1.5rem 1.25rem;
                border-radius: 18px;
            }

            .q-row {
                grid-template-columns: 1fr;
            }

            .q-step-head {
                flex-wrap: wrap;
            }

            .q-step-head small {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Get a Quote', 'url' => null]]])
<div class="ui-page">

    <section class="q-hero">
        <div class="h-wrap">
            <div class="q-hero-inner">
                <span class="h-eyebrow">Free freight quote</span>
                <h1 class="h-display">Get a price for your cargo</h1>
                <p class="h-lead">Tell us what you’re shipping and where. We’ll come back with a clear price and an estimated transit time.</p>
                <ul class="q-ticks">
                    <li><i class="fas fa-circle-check" aria-hidden="true"></i>Free, no obligation</li>
                    <li><i class="fas fa-circle-check" aria-hidden="true"></i>Price + transit time</li>
                    <li><i class="fas fa-circle-check" aria-hidden="true"></i>About 2 minutes to fill in</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="q-main">
        <div class="h-wrap q-grid">
            <div class="q-card">
                <form id="quoteForm" action="{{ route('quote.submit') }}" method="POST" novalidate>
                    @csrf
                    <div id="quoteFormError" class="quote-error-summary" role="alert" tabindex="-1" hidden></div>

                    <fieldset class="q-step">
                        <div class="q-step-head">
                            <span class="q-step-num">01</span>
                            <h2>Your shipment</h2>
                        </div>
                        <div class="q-field">
                            <label for="quote_service_type">Service *</label>
                            <select id="quote_service_type" name="service_type" required>
                                <option value="">Choose a service</option>
                                <option value="road-freight">Road Freight (Zambia / SADC)</option>
                                <option value="same-day">Same-Day Delivery (Lusaka)</option>
                                <option value="air-freight">Air Freight</option>
                                <option value="sea-freight">Sea Freight</option>
                                <option value="china-shopping">Shipping my China orders to Lusaka</option>
                                <option value="customs">Customs Clearance</option>
                                <option value="warehousing">Warehousing & Storage</option>
                                <option value="bulk-cargo">Bulk / Project Cargo</option>
                                <option value="other">Something else</option>
                            </select>
                        </div>
                        <div class="q-row">
                            <div class="q-field">
                                <label for="quote_pickup">Pickup location *</label>
                                <input id="quote_pickup" type="text" name="pickup" required placeholder="e.g. Johannesburg, South Africa">
                            </div>
                            <div class="q-field">
                                <label for="quote_delivery">Delivery location *</label>
                                <input id="quote_delivery" type="text" name="delivery" required placeholder="e.g. Lusaka, Zambia">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="q-step">
                        <div class="q-step-head">
                            <span class="q-step-num">02</span>
                            <h2>Cargo details</h2>
                            <small>Optional, but helps us price accurately</small>
                        </div>
                        <div class="q-row">
                            <div class="q-field">
                                <label for="quote_weight">Weight (kg) <span class="q-opt">(optional)</span></label>
                                <input id="quote_weight" type="number" name="weight" min="0" step="any" inputmode="decimal" placeholder="e.g. 250">
                            </div>
                            <div class="q-field">
                                <label for="quote_dimensions">Dimensions (L×W×H cm) <span class="q-opt">(optional)</span></label>
                                <input id="quote_dimensions" type="text" name="dimensions" placeholder="e.g. 120×80×100">
                            </div>
                        </div>
                        <div class="q-field">
                            <label for="quote_details">What are you shipping? <span class="q-opt">(optional)</span></label>
                            <textarea id="quote_details" name="details" rows="3" placeholder="Type of goods, number of packages, any deadlines or special handling…"></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="q-step">
                        <div class="q-step-head">
                            <span class="q-step-num">03</span>
                            <h2>Where should we send your quote?</h2>
                        </div>
                        <div class="q-row">
                            <div class="q-field">
                                <label for="quote_full_name">Full name *</label>
                                <input id="quote_full_name" type="text" name="full_name" autocomplete="name" required>
                            </div>
                            <div class="q-field">
                                <label for="quote_company">Company name <span class="q-opt">(optional)</span></label>
                                <input id="quote_company" type="text" name="company" autocomplete="organization">
                            </div>
                        </div>
                        <div class="q-row">
                            <div class="q-field">
                                <label for="quote_email">Email address *</label>
                                <input id="quote_email" type="email" name="email" autocomplete="email" required>
                            </div>
                            <div class="q-field">
                                <label for="quote_phone">Phone number *</label>
                                <input id="quote_phone" type="tel" name="phone" autocomplete="tel" required placeholder="+260 …">
                            </div>
                        </div>
                        <div class="q-consent">
                            <input type="checkbox" id="quote_consent" name="consent" required>
                            <label for="quote_consent">I agree to Forus Freight processing these details to prepare and respond to my quote request, in line with the <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</label>
                        </div>
                    </fieldset>

                    <button type="submit" class="h-btn h-btn--primary q-submit">Get my free quote <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                    <p class="q-fine">Free and with no obligation. Fields marked * are required.</p>
                </form>
            </div>

            <aside class="q-side">
                <div class="q-box">
                    <h3>What happens next</h3>
                    <ol class="q-next">
                        <li><span><strong>We review your request</strong>Our Lusaka team checks the route, service and cargo.</span></li>
                        <li><span><strong>You get a clear price</strong>With an estimated transit time, by email or phone.</span></li>
                        <li><span><strong>We take it from there</strong>Once you approve, we arrange pickup, paperwork and delivery.</span></li>
                    </ol>
                </div>

                <div class="q-box q-talk">
                    <h3>Prefer to talk?</h3>
                    <p>Send us the details on WhatsApp or give us a call during business hours.</p>
                    <div class="q-talk-actions">
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="h-btn h-btn--primary"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
                        <a href="tel:+260572788685" class="h-btn h-btn--ghost"><i class="fas fa-phone" aria-hidden="true"></i> +260 572 788 685</a>
                    </div>
                </div>

                @guest
                <div class="q-china">
                    <i class="fas fa-cart-shopping" aria-hidden="true"></i>
                    <div>
                        <strong>Shopping from China?</strong> You don’t need a quote to get started — create a free account for your personal China address.
                        <a href="{{ route('register') }}">Get my free China address &rarr;</a>
                    </div>
                </div>
                @endguest
            </aside>
        </div>
    </section>

</div>
<script>
    // Pre-select the service when arriving from a service's quote button (?service=road-freight)
    (function () {
        const wanted = new URLSearchParams(location.search).get('service');
        const select = document.getElementById('quote_service_type');
        if (wanted && select && select.querySelector('option[value="' + CSS.escape(wanted) + '"]')) {
            select.value = wanted;
        }
    })();

    document.getElementById('quoteForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Sending your request…';
        submitBtn.disabled = true;
        
        const errorBox = document.getElementById('quoteFormError');
        const form = this;

        function clearErrors() {
            errorBox.hidden = true;
            errorBox.textContent = '';
            form.querySelectorAll('.quote-field-error').forEach(el => el.remove());
            form.querySelectorAll('[aria-invalid]').forEach(el => {
                el.removeAttribute('aria-invalid');
                el.removeAttribute('aria-describedby');
            });
        }

        function fieldError(field, message) {
            const id = field.id + '-error';
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', id);
            const p = document.createElement('p');
            p.className = 'quote-field-error';
            p.id = id;
            p.textContent = message;
            (field.type === 'checkbox' ? field.parentElement : field).insertAdjacentElement('afterend', p);
        }

        function showSummary(message, firstInvalid) {
            errorBox.textContent = message;
            errorBox.hidden = false;
            (firstInvalid || errorBox).focus();
            if (!firstInvalid) errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function resetButton() {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }

        clearErrors();

        // Validate on the page, next to each field, instead of a browser alert
        let firstInvalid = null;
        let invalidCount = 0;
        this.querySelectorAll('[required]').forEach(field => {
            let message = '';
            if (field.type === 'checkbox' && !field.checked) {
                message = 'Please tick this box so we can process your request.';
            } else if (field.type !== 'checkbox' && !field.value.trim()) {
                const label = form.querySelector('label[for="' + field.id + '"]');
                message = 'Please enter your ' + (label ? label.textContent.replace('*', '').trim().toLowerCase() : 'details') + '.';
                if (field.tagName === 'SELECT') message = 'Please choose a service.';
            } else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())) {
                message = 'Please enter a valid email address, e.g. name@company.com.';
            }
            if (message) {
                fieldError(field, message);
                invalidCount++;
                firstInvalid = firstInvalid || field;
            }
        });

        if (invalidCount) {
            showSummary(invalidCount === 1 ? 'Please fix 1 field below.' : 'Please fix the ' + invalidCount + ' fields below.', firstInvalid);
            resetButton();
            return;
        }

        // Get form data
        const formData = new FormData(this);
        
        // Submit form via AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (response.status === 422 && data.errors) {
                let first = null;
                Object.entries(data.errors).forEach(([name, messages]) => {
                    const field = form.querySelector('[name="' + name + '"]');
                    if (field) {
                        fieldError(field, messages[0]);
                        first = first || field;
                    }
                });
                showSummary('Please check the highlighted fields.', first);
                resetButton();
                return;
            }
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Form submission failed');
            }
            form.reset();
            window.location.href = '{{ route('thank-you') }}?type=quote';
        })
        .catch(error => {
            console.error('Error:', error);
            showSummary('Sorry, we couldn\'t send your request just now. Please try again, or call +260 572 788 685 / message us on WhatsApp.');
            resetButton();
        });
    });
</script>
@endsection