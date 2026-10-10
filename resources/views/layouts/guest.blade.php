<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Forus Freight') }} - @yield('title', 'Authentication')</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
        <meta name="robots" content="noindex, nofollow">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Archivo:wdth,wght@100..125,500..800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/css/flag-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            :root {
                --primary: rgb(0, 127, 127);
                --secondary: rgb(255, 98, 0);
                --tertiary: rgb(204, 204, 204);
                --dark: #0f172a;
                --light: #ffffff;

                /* Same tokens as the marketing pages (partials/ui-base-styles) */
                --paper: #f5f4ef;
                --paper-2: #ebe9e2;
                --ink: #0d1b20;
                --ink-2: #33444a;
                --muted: #56666c;
                --teal-deep: #005e5e;
                --line: rgba(13, 27, 32, 0.12);
                --line-strong: rgba(13, 27, 32, 0.22);
                --display: 'Archivo', 'Inter', sans-serif;
                --mono: 'IBM Plex Mono', ui-monospace, monospace;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--paper);
                color: var(--ink);
            }

            a:focus-visible,
            button:focus-visible,
            input:focus-visible,
            select:focus-visible {
                outline: 3px solid var(--secondary);
                outline-offset: 3px;
            }

            /* ---------- Shell ---------- */
            .auth-shell {
                min-height: 100vh;
                display: grid;
                grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            }

            /* ---------- Left: brand panel ---------- */
            .auth-side {
                /* stays put while a long form (e.g. registration) scrolls */
                position: sticky;
                top: 0;
                height: 100vh;
                align-self: start;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 3rem;
                padding: 2.5rem 3.5rem;
                background:
                    radial-gradient(ellipse at 20% 85%, rgba(0, 150, 150, 0.35), transparent 55%),
                    linear-gradient(160deg, #0c2a33 0%, #071b22 100%);
                color: #fff;
            }

            .auth-side::before {
                /* the dotted network map from the homepage, very faint */
                content: '';
                position: absolute;
                inset: 18% -20% -5% -10%;
                background: url('{{ asset('images/network-map-dots.svg') }}') center / contain no-repeat;
                filter: brightness(3);
                opacity: 0.12;
                pointer-events: none;
            }

            .auth-side > * {
                position: relative;
            }

            .auth-logo {
                display: inline-flex;
                background: #fff;
                border-radius: 16px;
                padding: 0.6rem 0.85rem;
                align-self: flex-start;
                transition: transform 0.2s ease;
            }

            .auth-logo:hover {
                transform: translateY(-2px);
            }

            .auth-logo img {
                height: 64px;
                width: auto;
                display: block;
            }

            .auth-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                font-family: var(--mono);
                font-size: 0.75rem;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: #5eead4;
            }

            .auth-eyebrow::before {
                content: '';
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: var(--secondary);
            }

            .auth-side h1 {
                font-family: var(--display);
                font-stretch: 110%;
                font-weight: 800;
                font-size: clamp(2.1rem, 3.2vw, 2.9rem);
                line-height: 1.05;
                letter-spacing: -0.02em;
                margin: 1rem 0 1rem;
                max-width: 15ch;
            }

            .auth-side-lead {
                color: #c4d1d5;
                font-size: 1.02rem;
                line-height: 1.6;
                max-width: 30rem;
            }

            .auth-features {
                list-style: none;
                margin: 2rem 0 0;
                padding: 0;
                max-width: 30rem;
                border-top: 1px solid rgba(255, 255, 255, 0.14);
            }

            .auth-features li {
                display: grid;
                grid-template-columns: 2.5rem 1fr;
                align-items: start;
                padding: 0.9rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.14);
                color: #dfe8ea;
                font-size: 0.95rem;
                line-height: 1.45;
            }

            .auth-features i {
                color: var(--secondary);
                margin-top: 0.2rem;
            }

            .auth-features strong {
                display: block;
                color: #fff;
            }

            .auth-side-foot {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 0.75rem 1.5rem;
                font-size: 0.88rem;
                color: #8fa6ad;
            }

            .auth-side-foot a {
                color: #5eead4;
                font-weight: 600;
                text-decoration: none;
            }

            .auth-side-foot a:hover {
                text-decoration: underline;
            }

            /* ---------- Right: form area ---------- */
            .auth-main {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                padding: 1.5rem 2rem 1.75rem;
            }

            .auth-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }

            .auth-topbar .auth-mobile-logo img {
                height: 52px;
                width: auto;
                display: block;
            }

            .auth-content {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2.5rem 0;
            }

            .auth-content > div {
                width: 100%;
                max-width: 440px;
            }

            /* Page headings on every auth page */
            .auth-content h2 {
                font-family: var(--display);
                font-stretch: 108%;
                letter-spacing: -0.02em !important;
                color: var(--ink) !important;
            }

            /* The form cards on every auth page */
            .auth-content .shadow-2xl {
                box-shadow: 0 24px 50px rgba(13, 27, 32, 0.08) !important;
                border-color: var(--line) !important;
                border-radius: 24px !important;
            }

            .auth-content label {
                color: var(--ink) !important;
            }

            .auth-foot {
                text-align: center;
                font-size: 0.85rem;
                color: var(--muted);
            }

            /* Sign-up visual: a parcel label with the personal parts blurred */
            .auth-label {
                margin: 2rem 0 0;
                max-width: 340px;
                background: #fffdf7;
                color: var(--ink);
                border-radius: 14px;
                padding: 1.1rem 1.25rem 1rem;
                font-family: var(--mono);
                font-size: 0.78rem;
                line-height: 1.5;
                transform: rotate(-1.5deg);
                box-shadow: 0 24px 50px rgba(0, 0, 0, 0.4);
            }

            .auth-label-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-bottom: 0.6rem;
                margin-bottom: 0.7rem;
                border-bottom: 2px solid var(--ink);
            }

            .auth-label-top span {
                font-family: var(--display);
                font-stretch: 115%;
                font-weight: 800;
                font-size: 0.9rem;
            }

            .auth-label-top b {
                background: var(--ink);
                color: #fff;
                font-weight: 500;
                font-size: 0.68rem;
                padding: 0.15rem 0.45rem;
                border-radius: 5px;
            }

            .auth-label small {
                display: block;
                font-size: 0.62rem;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .auth-label strong {
                display: block;
                font-family: var(--display);
                font-stretch: 110%;
                font-size: 1.45rem;
                color: var(--secondary);
                margin-bottom: 0.6rem;
            }

            .auth-label em {
                font-style: normal;
                filter: blur(4px);
            }

            .auth-label p {
                margin: 0 0 0.7rem;
            }

            .auth-label-code {
                height: 30px;
                background: repeating-linear-gradient(90deg, var(--ink) 0 2px, transparent 2px 5px, var(--ink) 5px 6px, transparent 6px 9px, var(--ink) 9px 12px, transparent 12px 14px);
                opacity: 0.85;
            }

            .auth-ticks {
                list-style: none;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem 1.5rem;
                margin: 2rem 0 0;
                padding: 0;
                color: #dfe8ea;
                font-weight: 600;
                font-size: 0.92rem;
            }

            .auth-ticks i {
                color: var(--secondary);
                margin-right: 0.45rem;
            }

            /* Sign-up progress strip */
            .auth-steps {
                list-style: none;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.4rem;
                margin: 0 0 1.75rem;
                padding: 0;
            }

            .auth-steps li {
                padding-top: 0.6rem;
                border-top: 3px solid var(--line-strong);
                font-size: 0.78rem;
                font-weight: 600;
                color: var(--muted);
                line-height: 1.3;
            }

            .auth-steps li span {
                display: block;
                font-family: var(--mono);
                font-size: 0.68rem;
                font-weight: 500;
                letter-spacing: 0.06em;
                color: var(--muted);
            }

            .auth-steps li.is-current {
                border-top-color: var(--secondary);
                color: var(--ink);
            }

            .auth-steps li.is-current span {
                color: #b54400;
            }

            .field-hint {
                margin-top: 0.4rem;
                font-size: 0.8rem;
                color: var(--muted);
            }

            .pw-toggle {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                background: none;
                color: var(--muted);
                cursor: pointer;
                z-index: 2;
                border-radius: 0 12px 12px 0;
            }

            .pw-toggle:hover {
                color: var(--ink);
            }

            .form-control.has-toggle {
                padding-right: 3rem !important;
            }

            /* ---------- Shared form controls (used by all auth pages) ---------- */
            .btn-auth {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.6rem;
                width: 100%;
                min-height: 50px;
                padding: 0 1.5rem;
                border: none;
                border-radius: 10px;
                background: var(--secondary);
                color: var(--ink);
                font-weight: 800;
                font-size: 1rem;
                text-decoration: none;
                cursor: pointer;
                transition: background-color 0.2s ease, transform 0.2s ease;
            }

            .btn-auth:hover {
                background: #ff7a26;
            }

            .btn-auth:active {
                transform: translateY(1px);
            }

            .btn-auth:disabled {
                opacity: 0.7;
                cursor: wait;
            }

            /* Override @tailwindcss/forms resets for .form-control inputs */
            .input-wrapper {
                position: relative;
                display: block;
                width: 100%;
            }

            .input-icon {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                display: flex;
                align-items: center;
                padding-left: 1rem;
                pointer-events: none;
                color: #8a979c;
                z-index: 1;
            }

            .form-control {
                display: block;
                width: 100%;
                min-height: 48px;
                border-radius: 10px;
                border: 1.5px solid #cfd5d3 !important;
                padding: 0.7rem 0.95rem !important;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
                background-color: #fff;
                font-size: 1rem;
                line-height: 1.5;
                color: var(--ink);
                box-sizing: border-box;
            }

            .form-control.has-icon {
                padding-left: 2.75rem !important;
            }

            .form-control:hover {
                border-color: #aab4b1 !important;
            }

            .form-control:focus {
                border-color: var(--primary) !important;
                outline: none;
                box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.16) !important;
            }

            .form-control::placeholder {
                color: #8a979c;
            }

            select.form-control {
                appearance: auto;
                cursor: pointer;
                background-image: none;
                padding-right: 0.75rem;
            }

            /* Phone input group */
            .phone-input-group {
                border-radius: 10px;
                border: 1.5px solid #cfd5d3;
                overflow: hidden;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
                background: #fff;
            }

            .phone-input-group:hover {
                border-color: #aab4b1;
            }

            .phone-input-group:focus-within {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.16);
            }

            .phone-country-select {
                border: none;
                border-right: 1.5px solid #cfd5d3;
                background: #f7f7f4;
                padding: 0.75rem 0.5rem 0.75rem 1rem;
                font-size: 0.9375rem;
                font-weight: 600;
                color: var(--ink);
                cursor: pointer;
                outline: none;
                min-width: 85px;
                appearance: auto;
                -webkit-appearance: auto;
                -moz-appearance: auto;
            }

            .phone-country-select:focus {
                outline: none;
                box-shadow: none;
            }

            /* Override browser default blue on select focus */
            select:focus,
            select:-moz-focusring {
                outline: none;
                box-shadow: none;
            }

            /* WebKit/Blink: remove default blue highlight */
            select::-webkit-focus-inner {
                border: none;
                padding: 0;
            }

            .phone-number-wrapper {
                border: none !important;
                border-radius: 0 !important;
            }

            .phone-number-input {
                display: block;
                width: 100%;
                border: none !important;
                border-radius: 0 !important;
                padding: 0.75rem 1rem !important;
                min-height: 48px;
                font-size: 1rem;
                line-height: 1.5;
                color: var(--ink);
                background: transparent;
                outline: none;
            }

            .phone-number-input::placeholder {
                color: #8a979c;
            }

            @media (max-width: 1023px) {
                .auth-shell {
                    grid-template-columns: 1fr;
                }

                .auth-side {
                    display: none;
                }
            }

            @media (min-width: 1024px) {
                .auth-mobile-logo {
                    display: none;
                }
            }

            @media (max-width: 600px) {
                .auth-main {
                    padding: 1rem 1.1rem 1.25rem;
                }

                .auth-content {
                    padding: 1.5rem 0;
                }

                .auth-content .p-8 {
                    padding: 1.5rem !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    transition-duration: 0.01ms !important;
                    animation-duration: 0.01ms !important;
                }
            }
        </style>
    </head>
    <body class="antialiased h-full">
        <div class="auth-shell">
            <!-- Left: what the customer account gives you -->
            <aside class="auth-side">
                <a href="{{ route('home') }}" class="auth-logo" aria-label="Forus Freight home">
                    <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
                </a>

                @if (request()->routeIs('register'))
                <div>
                    <span class="auth-eyebrow">Free for online shoppers</span>
                    <h1>Your own address in China. Free.</h1>
                    <p class="auth-side-lead">Shop on Alibaba, Amazon, Shein, Temu and more. We receive your parcels, combine them and ship them to Lusaka.</p>

                    <figure class="auth-label" aria-hidden="true">
                        <div class="auth-label-top"><span>FORUS FREIGHT</span><b>FUO &rarr; LUSAKA</b></div>
                        <small>Shipping mark</small>
                        <strong>ZMFFL <em>406118</em></strong>
                        <small>Deliver to</small>
                        <p><em>Your name &middot; +260 97 000 0000</em><br>Forus Freight Warehouse, B18<br>Lijin Logistics Park, Foshan</p>
                        <div class="auth-label-code"></div>
                    </figure>

                    <ul class="auth-ticks">
                        <li><i class="fas fa-check" aria-hidden="true"></i>Free account &amp; address</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i>Parcels combined</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i>Collect in Lusaka</li>
                    </ul>
                </div>
                @else
                <div>
                    <span class="auth-eyebrow">Customer portal</span>
                    <h1>Your shipments, all in one place</h1>
                    <p class="auth-side-lead">Sign in to follow every shipment, find your China address and keep on top of invoices.</p>
                    <ul class="auth-features">
                        <li><i class="fas fa-location-crosshairs" aria-hidden="true"></i><span><strong>Track every shipment</strong>Live status and the full timeline, from China to Lusaka.</span></li>
                        <li><i class="fas fa-warehouse" aria-hidden="true"></i><span><strong>Your China address</strong>Your personal Foshan warehouse address and shipping mark.</span></li>
                        <li><i class="fas fa-box-open" aria-hidden="true"></i><span><strong>New shipment requests</strong>Tell us what’s coming and we’ll handle the rest.</span></li>
                        <li><i class="fas fa-file-invoice" aria-hidden="true"></i><span><strong>Invoices &amp; payments</strong>See what you owe and pay online.</span></li>
                    </ul>
                </div>
                @endif

                <div class="auth-side-foot">
                    <span>&copy; {{ date('Y') }} Forus Freight Ltd</span>
                    <a href="https://wa.me/260572788685?text={{ urlencode('Hi Forus Freight, I need help with my account') }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> Need help? WhatsApp us</a>
                </div>
            </aside>

            <!-- Right: the form -->
            <main class="auth-main">
                <div class="auth-topbar">
                    <a href="{{ route('home') }}" class="auth-mobile-logo" aria-label="Forus Freight home">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
                    </a>
                </div>

                <div class="auth-content">
                    <div>
                        {{ $slot }}
                    </div>
                </div>

                <p class="auth-foot lg:hidden">&copy; {{ date('Y') }} Forus Freight Ltd</p>
            </main>
        </div>
        @livewireScripts
    </body>
</html>
