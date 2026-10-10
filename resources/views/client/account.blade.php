@extends('layouts.dashboard')

@section('title', 'Account - Forus Freight')

@section('styles')
<style>
    .ac {
        /* Same tokens as partials/ui-base-styles (see create-shipment). */
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
    }

    .ac h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        letter-spacing: -0.02em;
    }

    .ac-sub {
        margin-top: 0.35rem;
        color: var(--ink-2);
    }

    .ac-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 1.5rem;
        margin-top: 1.25rem;
        scroll-margin-top: 1rem;
    }

    .ac-card-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.1rem;
    }

    .ac-card-head h2 {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .ac-card-head a {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--teal-deep);
        text-decoration: none;
    }

    .ac-card-head a:hover { text-decoration: underline; }

    /* Identity strip */
    .ac-ident {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--line);
    }

    .ac-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--ink);
        color: #fff;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .ac-ident-name {
        display: block;
        font-weight: 700;
        font-size: 1.05rem;
        line-height: 1.3;
    }

    .ac-ident-meta {
        display: block;
        font-size: 0.85rem;
        color: var(--muted);
        line-height: 1.5;
    }

    .ac-verified {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: #166534;
        font-weight: 600;
    }

    /* Forms */
    .ac-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.1rem 1.25rem;
    }

    .ac-span { grid-column: 1 / -1; }

    .ac-field {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        min-width: 0;
    }

    .ac-field > label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .ac-in {
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
        transition: border-color 0.15s ease;
    }

    .ac-in:focus {
        outline: none;
        border-color: var(--teal);
    }

    textarea.ac-in {
        min-height: 96px;
        resize: vertical;
        font-weight: 500;
    }

    .ac-err {
        color: #b91c1c;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .ac-hint {
        font-size: 0.78rem;
        color: var(--muted);
        line-height: 1.5;
    }

    .ac-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin-top: 1.35rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--line);
    }

    .ac-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 46px;
        padding: 0 1.5rem;
        border: none;
        border-radius: 12px;
        background: var(--teal);
        color: #fff;
        font: inherit;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .ac-btn:hover { background: #006666; }

    /* Banners */
    .ac-banner {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        margin-bottom: 1.25rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .ac-banner-ok {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .ac-banner i { margin-top: 0.15rem; }

    /* Invoices */
    .ac-outstanding {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.9rem 1.1rem;
        margin-bottom: 1rem;
        background: #faf9f5;
        border: 1px solid var(--line);
        border-radius: 12px;
    }

    .ac-outstanding span {
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--muted);
    }

    .ac-outstanding strong {
        font-size: 1.25rem;
        color: #b91c1c;
    }

    .ac-inv {
        list-style: none;
        border-top: 1px solid var(--line);
    }

    .ac-inv li:first-child { border-top: none; }

    .ac-row {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr) auto;
        gap: 0.35rem 1.25rem;
        align-items: center;
        padding: 0.95rem 0;
    }

    .ac-inv-ref {
        font-family: var(--mono);
        font-weight: 500;
        font-size: 0.92rem;
        color: inherit;
        text-decoration: none;
    }

    .ac-inv-ref:hover { color: var(--teal-deep); }

    .ac-inv-meta {
        display: block;
        font-size: 0.83rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ac-inv-meta a {
        color: var(--teal-deep);
        font-weight: 600;
        text-decoration: none;
    }

    .ac-inv-meta a:hover { text-decoration: underline; }

    .ac-chip {
        justify-self: start;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        white-space: nowrap;
    }

    .ac-chip-pending { background: #fff7ed; color: #c2410c; }
    .ac-chip-paid { background: #dcfce7; color: #166534; }
    .ac-chip-other { background: #eef2ff; color: #3730a3; }

    .ac-inv-end {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.9rem;
    }

    .ac-amount {
        font-weight: 800;
        font-size: 1rem;
        text-align: right;
        white-space: nowrap;
    }

    .ac-amount small {
        display: block;
        font-weight: 600;
        font-size: 0.75rem;
        color: var(--muted);
    }

    .ac-amount small.is-late { color: #b91c1c; }

    .ac-pay {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 40px;
        padding: 0 0.9rem;
        border: 1px solid var(--line-strong);
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .ac-pay:hover { border-color: var(--ink); }

    .ac-pay.is-primary {
        background: var(--orange);
        border-color: var(--orange);
        color: var(--ink);
    }

    .ac-pay.is-primary:hover { background: #ff7a26; border-color: #ff7a26; }

    .ac-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: var(--ink-2);
        font-size: 0.95rem;
    }

    .ac-pages {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .ac-pages a,
    .ac-pages span {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 1rem;
        border-radius: 10px;
        border: 1px solid var(--line-strong);
        background: #fff;
        color: var(--ink);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .ac-pages span {
        color: var(--muted);
        border-color: transparent;
        background: none;
    }

    /* Help */
    .ac-help {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .ac-help a {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        min-height: 58px;
        padding: 0.85rem 1rem;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fafaf7;
        color: var(--ink);
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .ac-help a:hover {
        border-color: var(--ink);
        background: #fff;
    }

    .ac-help a i {
        color: var(--teal);
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    .ac-faq {
        margin-top: 1.25rem;
        padding-top: 1.1rem;
        border-top: 1px solid var(--line);
        display: grid;
        gap: 0.35rem;
    }

    .ac-faq details {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
    }

    .ac-faq summary {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 1rem;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 700;
        list-style: none;
    }

    .ac-faq summary::-webkit-details-marker { display: none; }

    .ac-faq summary::after {
        content: '+';
        margin-left: auto;
        font-weight: 700;
        color: var(--muted);
        font-size: 1.05rem;
    }

    .ac-faq details[open] summary::after { content: '–'; }

    .ac-faq summary i { color: var(--teal); }

    .ac-faq p {
        padding: 0 1rem 1rem 2.35rem;
        font-size: 0.88rem;
        color: var(--muted);
        line-height: 1.6;
    }

    .ac-faq p a {
        color: var(--teal-deep);
        font-weight: 600;
    }

    @media (max-width: 720px) {
        .ac-grid { grid-template-columns: 1fr; }
        .ac-card { padding: 1.25rem 1.1rem; scroll-margin-top: 70px; }
        .ac-help { grid-template-columns: 1fr; }

        .ac-row { grid-template-columns: minmax(0, 1fr) auto; }
        .ac-chip { grid-column: 1 / -1; grid-row: 2; }
        .ac-inv-end { grid-column: 1 / -1; grid-row: 3; justify-content: space-between; }
    }
</style>
@endsection

@section('content')
@php
    $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'FF';
    $whatsapp = '260572788685';
    $invoiceStatus = fn ($invoice) => $invoice->status === 'Pending' ? 'pending' : ($invoice->status === 'Paid' ? 'paid' : 'other');
@endphp

<div class="ac">
    <h1>Account</h1>
    <p class="ac-sub">Your details, password, invoices and help — all in one place.</p>

    {{-- Profile --}}
    <section class="ac-card" id="profile" aria-labelledby="acProfileH">
        <div class="ac-card-head">
            <h2 id="acProfileH">Profile</h2>
        </div>

        <div class="ac-ident">
            <span class="ac-avatar" aria-hidden="true">{{ $initials }}</span>
            <span style="min-width: 0;">
                <span class="ac-ident-name">{{ $user->name }}</span>
                <span class="ac-ident-meta">
                    Client since {{ $user->created_at->format('M Y') }}
                    @if($user->phone)
                        · {{ $user->phone }}
                    @endif
                    @if($user->phone_verified_at)
                        · <span class="ac-verified"><i class="fas fa-circle-check" aria-hidden="true"></i> Verified</span>
                    @endif
                </span>
            </span>
        </div>

        <form action="{{ route('client.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="ac-grid">
                <div class="ac-field">
                    <label for="ac_name">Full name</label>
                    <input id="ac_name" type="text" name="name" class="ac-in" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                    @error('name')<span class="ac-err">{{ $message }}</span>@enderror
                </div>
                <div class="ac-field">
                    <label for="ac_email">Email</label>
                    <input id="ac_email" type="email" name="email" class="ac-in" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                    @error('email')<span class="ac-err">{{ $message }}</span>@enderror
                </div>
                <div class="ac-field">
                    <label for="ac_company">Company <span style="font-weight: 600; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input id="ac_company" type="text" name="company_name" class="ac-in" value="{{ old('company_name', $user->company_name) }}" placeholder="e.g. Chunga Trading">
                </div>
                <div class="ac-field">
                    <label for="ac_phone">Phone</label>
                    <input id="ac_phone" type="tel" class="ac-in" value="{{ $user->phone ?? '' }}" style="background: #f6f5f0;" readonly aria-describedby="ac_phone_hint">
                    <span class="ac-hint" id="ac_phone_hint">Your phone can’t be changed here — <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hi Forus Freight, I would like to change the phone number on my account.') }}" target="_blank" rel="noopener" style="color: var(--teal-deep); font-weight: 700;">ask us on WhatsApp</a> so we can verify it first.</span>
                </div>
                <div class="ac-field ac-span">
                    <label for="ac_address">Default shipping address</label>
                    <textarea id="ac_address" name="address" class="ac-in" rows="3" placeholder="Your usual delivery address">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>

            <div class="ac-actions">
                <button type="submit" class="ac-btn">Save changes</button>
            </div>
        </form>
    </section>

    {{-- Password --}}
    <section class="ac-card" id="password" aria-labelledby="acPasswordH">
        <div class="ac-card-head">
            <h2 id="acPasswordH">Password</h2>
        </div>

        @if(session('status') === 'password-updated')
            <div class="ac-banner ac-banner-ok" role="status">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <span>Your password has been updated.</span>
            </div>
        @endif

        @if(session('status') === 'password-updated' || $errors->hasBag('updatePassword'))
            {{-- The form posts back to the top of the page — bring the
                 confirmation (or the error) into view. --}}
            <script>document.getElementById('password').scrollIntoView({ block: 'start' });</script>
        @endif

        <form action="{{ route('client.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="ac-grid">
                <div class="ac-field ac-span">
                    <label for="ac_current_password">Current password</label>
                    <input id="ac_current_password" type="password" name="current_password" class="ac-in" autocomplete="current-password" required>
                    @error('current_password', 'updatePassword')<span class="ac-err">{{ $message }}</span>@enderror
                </div>
                <div class="ac-field">
                    <label for="ac_password">New password</label>
                    <input id="ac_password" type="password" name="password" class="ac-in" autocomplete="new-password" required>
                    @error('password', 'updatePassword')<span class="ac-err">{{ $message }}</span>@enderror
                </div>
                <div class="ac-field">
                    <label for="ac_password_confirmation">Repeat new password</label>
                    <input id="ac_password_confirmation" type="password" name="password_confirmation" class="ac-in" autocomplete="new-password" required>
                </div>
            </div>

            <div class="ac-actions">
                <button type="submit" class="ac-btn">Update password</button>
                <span class="ac-hint">At least 8 characters.</span>
            </div>
        </form>
    </section>

    {{-- Invoices --}}
    <section class="ac-card" id="invoices" aria-labelledby="acInvoicesH">
        <div class="ac-card-head">
            <h2 id="acInvoicesH">Invoices</h2>
            @if($outstanding > 0)
                <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hi Forus Freight, I would like to pay my outstanding invoices. Please send payment details.') }}" target="_blank" rel="noopener">Pay all on WhatsApp</a>
            @endif
        </div>

        @if($outstanding > 0)
            <div class="ac-outstanding">
                <span>Outstanding</span>
                <strong>{{ money($outstanding, $outstandingCurrency) }}</strong>
            </div>
        @endif

        @if($invoices->isEmpty())
            <p class="ac-empty">No invoices yet — they appear here once our team issues one.</p>
        @else
            <ul class="ac-inv">
                @foreach($invoices as $invoice)
                    @php
                        $statusClass = $invoiceStatus($invoice);
                        $late = $invoice->due_date && $invoice->due_date->isPast() && $invoice->status === 'Pending';
                    @endphp
                    <li>
                        <div class="ac-row">
                            <span style="min-width: 0;">
                                <a class="ac-inv-ref" href="{{ route('client.invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                                <span class="ac-inv-meta">
                                    @if($invoice->shipment)
                                        <a href="{{ route('tracking.show', $invoice->shipment->serial_no) }}">{{ $invoice->shipment->serial_no }}</a>
                                    @else
                                        No shipment linked
                                    @endif
                                </span>
                            </span>
                            <span class="ac-chip ac-chip-{{ $statusClass }}">{{ $invoice->status }}</span>
                            <span class="ac-inv-end">
                                <span class="ac-amount">
                                    {{ money($invoice->amount, $invoice->currency) }}
                                    <small class="{{ $late ? 'is-late' : '' }}">
                                        @if($invoice->status === 'Paid' && $invoice->paid_at)
                                            Paid {{ $invoice->paid_at->format('j M Y') }}
                                        @elseif($invoice->due_date)
                                            {{ $late ? 'Overdue · ' : 'Due ' }}{{ $invoice->due_date->format('j M Y') }}
                                        @else
                                            On receipt
                                        @endif
                                    </small>
                                </span>
                                @if($invoice->status === 'Pending')
                                    @if($invoice->canPayOnline())
                                        <a class="ac-pay is-primary" href="{{ route('client.payments.checkout', $invoice) }}">
                                            <i class="fas fa-credit-card" aria-hidden="true"></i> Pay
                                        </a>
                                    @else
                                        <a class="ac-pay" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hi Forus Freight, I would like to pay invoice ' . $invoice->invoice_number . '. Please send payment details.') }}" target="_blank" rel="noopener">
                                            <i class="fab fa-whatsapp" aria-hidden="true"></i> How to pay
                                        </a>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if($invoices->hasPages())
                <nav class="ac-pages" aria-label="Invoice pages">
                    @if($invoices->previousPageUrl())<a href="{{ $invoices->previousPageUrl() }}#invoices">← Newer</a>@endif
                    <span>Page {{ $invoices->currentPage() }} of {{ $invoices->lastPage() }}</span>
                    @if($invoices->nextPageUrl())<a href="{{ $invoices->nextPageUrl() }}#invoices">Older →</a>@endif
                </nav>
            @endif
        @endif
    </section>

    {{-- Help --}}
    <section class="ac-card" id="help" aria-labelledby="acHelpH">
        <div class="ac-card-head">
            <h2 id="acHelpH">Help</h2>
        </div>

        <div class="ac-help">
            <a href="{{ route('client.getting-started') }}"><i class="fas fa-graduation-cap" aria-hidden="true"></i> How it works</a>
            <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hi Forus Freight, I need help with my shipment.') }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
            <a href="mailto:info@forusfreight.com"><i class="fas fa-envelope" aria-hidden="true"></i> Email us</a>
        </div>

        <div class="ac-faq">
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> How do I track my shipment?</summary>
                <p>Open <strong>Shipments</strong> in the menu to see every shipment’s status and progress, or use the public <a href="{{ route('track') }}">/track</a> page with your Serial Number (e.g. <strong>RS.12345</strong>) or carrier tracking number.</p>
            </details>
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> Which address should I use for my online orders?</summary>
                <p>Ship every online order to our China warehouse: <strong>{{ config('forus.forwarding_address.name') }}</strong>, {{ config('forus.forwarding_address.line1') }}, {{ config('forus.forwarding_address.line2') }}, {{ config('forus.forwarding_address.line3') }}, {{ config('forus.forwarding_address.country') }} ({{ config('forus.forwarding_address.address_cn') }}). Contact: {{ config('forus.forwarding_address.phone') }} / {{ config('forus.forwarding_address.email') }}. Your personal version — with your name and phone — is on the <a href="{{ route('dashboard') }}">Home page</a>.</p>
            </details>
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> How do I name my parcel so it comes to Zambia, not Ghana?</summary>
                <p>Always add our shipping mark <strong>ZMFFL</strong> together with your <strong>name and phone</strong> on each parcel. <strong>ZMFFL</strong> routes parcels to <strong>Zambia</strong> (<strong>GHFFL</strong> would route them to Ghana). The Home page shows exactly what to write, ready to copy.</p>
            </details>
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> What documents will I need?</summary>
                <p>For most regional shipments: Bill of Lading, Commercial Invoice and Packing List. You don’t need to upload anything here — send them to us on WhatsApp whenever our team asks.</p>
            </details>
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> How can I settle outstanding invoices?</summary>
                <p>Press <strong>How to pay</strong> next to any invoice above (or on the <a href="{{ route('dashboard') }}">Home page</a>) — it opens WhatsApp with your invoice number so we can send you bank transfer or mobile money details.</p>
            </details>
            <details>
                <summary><i class="fas fa-circle-question" aria-hidden="true"></i> What areas do you operate in?</summary>
                <p>We provide freight solutions across Zambia and the entire SADC region, including South Africa, DRC, Zimbabwe, and Tanzania.</p>
            </details>
        </div>
    </section>
</div>
@endsection
