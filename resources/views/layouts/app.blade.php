<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Forus Freight - Global Logistics Solutions')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-180.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Canonical -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Meta Description -->
    <meta name="description" content="@yield('meta_description', 'Forus Freight Limited is a Zambian logistics company providing road, air, and sea freight, customs brokerage, and warehousing across Zambia and the SADC region.')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="theme-color" content="#007f7f">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Forus Freight">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Forus Freight - Global Logistics Solutions')">
    <meta property="og:description" content="@yield('meta_description', 'Forus Freight Limited is a Zambian logistics company providing road, air, and sea freight, customs brokerage, and warehousing across Zambia and the SADC region.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:image:alt" content="Forus Freight logistics services">
    <meta property="og:locale" content="en_ZM">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Forus Freight - Global Logistics Solutions')">
    <meta name="twitter:description" content="@yield('meta_description', 'Forus Freight Limited is a Zambian logistics company providing road, air, and sea freight, customs brokerage, and warehousing across Zambia and the SADC region.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta name="twitter:image:alt" content="Forus Freight logistics services">

    <!-- Structured Data: Organization / LocalBusiness -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Forus Freight Limited",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo-transparent.png') }}",
        "image": "{{ asset('images/logo-transparent.png') }}",
        "telephone": "+260572788685",
        "email": "info@forusfreight.com",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "METROLUX PLAZA, Plot No. 401A/8 Kafue Road",
            "addressLocality": "Lusaka",
            "addressCountry": "ZM"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "08:00",
            "closes": "17:00"
        },
        "areaServed": {
            "@type": "Place",
            "name": "Zambia and the SADC region"
        }
    }
    </script>

    <!-- Structured Data: WebSite -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Forus Freight",
        "url": "{{ url('/') }}"
    }
    </script>

    @yield('structured_data')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Archivo:wdth,wght@100..125,500..800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: rgb(0, 127, 127); /* Main background */
            color: #ffffff;
            overflow-x: hidden;
            line-height: 1.6;
        }

        body.legal-page { background: var(--paper); color: var(--ink); }
        body.legal-page section[style*="background: #f8fafc"] { background: var(--paper) !important; }
        body.legal-page article { border-color: var(--line) !important; color: var(--ink-2) !important; box-shadow: 0 18px 45px rgba(13,27,32,.07) !important; }
        body.legal-page article h2, body.legal-page article h3 { color: var(--teal-deep) !important; }
        body.legal-page article p, body.legal-page article li { color: var(--ink-2) !important; }

        /* Color Variables */
        :root {
            --primary: rgb(0, 127, 127);    /* Teal/Blue */
            --secondary: rgb(255, 98, 0);   /* Orange */
            --tertiary: rgb(204, 204, 204); /* Light Gray */
            --dark: #0f172a;
            --light: #ffffff;
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        /* Skip link + keyboard focus */
        .skip-link {
            position: absolute;
            left: 1rem;
            top: -100px;
            z-index: 3000;
            background: var(--dark);
            color: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 0 0 12px 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .skip-link:focus {
            top: 0;
        }

        main:focus {
            outline: none;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: 3px solid var(--secondary);
            outline-offset: 3px;
        }

        .logo-img {
            height: 88px;
            width: auto;
            display: block;
        }

        .logo-icon {
            width: 0;
            height: 0;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, rgb(0, 150, 150) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-link {
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--primary);
            transition: width 0.3s ease;
            border-radius: 3px;
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }

        .nav-btn {
            background: var(--primary);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .nav-btn:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 127, 127, 0.25);
        }

        .nav-btn-outline {
            background: white;
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .nav-btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 127, 127, 0.25);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 127, 127, 0.3);
        }

        .btn-primary:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 127, 127, 0.4);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid var(--secondary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.3);
        }

        .btn-secondary:hover {
            background: white;
            color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 98, 0, 0.4);
        }

        .btn-tertiary {
            background: var(--tertiary);
            color: #1e293b;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid var(--tertiary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(204, 204, 204, 0.3);
        }

        .btn-tertiary:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(204, 204, 204, 0.4);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(0, 127, 127, 0.1);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: rgba(0, 127, 127, 0.2);
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            max-height: calc(100vh - 100%);
            overflow-y: auto;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 2rem 1rem;
            border-radius: 0 0 20px 20px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu ul {
            list-style: none;
            padding: 0;
        }

        .mobile-menu li {
            margin-bottom: 0.75rem;
        }

        .mobile-menu a {
            display: block;
            padding: 1rem 1.5rem;
            color: #1e293b;
            text-decoration: none;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(0, 127, 127, 0.05);
        }

        .mobile-menu a:hover, .mobile-menu a.active {
            background: var(--primary);
            color: white;
            transform: translateX(5px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, rgba(0, 127, 127, 0.9) 100%);
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, white 0%, rgba(255,255,255,0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 127, 127, 0.1);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 127, 127, 0.15);
            border-color: var(--primary);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: white;
        }

        .card-primary .card-icon {
            background: var(--primary);
        }

        .card-secondary .card-icon {
            background: var(--secondary);
        }

        .card-tertiary .card-icon {
            background: var(--tertiary);
            color: #1e293b;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .card-text {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        /* Sections */
        .section {
            padding: 5rem 0;
        }

        .section-bg-white {
            background: white;
        }

        .section-bg-light {
            background: rgba(255, 255, 255, 0.05);
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: white;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.92);
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .section-title-dark {
            color: #1e293b;
        }

        .section-subtitle-dark {
            color: #64748b;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-cols-1 {
            grid-template-columns: 1fr;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        /* Footer */
        .site-footer {
            --f-ink: #0d1b20;
            --f-text: #c4d1d5;
            --f-muted: #8fa6ad;
            --f-line: rgba(255, 255, 255, 0.12);
            background: var(--f-ink);
            color: var(--f-text);
            font-size: 0.95rem;
        }

        .site-footer a {
            color: inherit;
            text-decoration: none;
        }

        .f-wrap {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .f-cta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem 2rem;
            padding: 3.5rem 0;
            border-bottom: 1px solid var(--f-line);
        }

        .f-cta h2 {
            font-family: 'Archivo', 'Inter', sans-serif;
            font-stretch: 110%;
            font-weight: 800;
            font-size: clamp(1.8rem, 3.6vw, 2.6rem);
            letter-spacing: -0.02em;
            line-height: 1.05;
            color: #fff;
        }

        .f-cta p {
            margin-top: 0.5rem;
            color: var(--f-text);
        }

        .f-cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .f-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-height: 50px;
            padding: 0 1.4rem;
            border-radius: 12px;
            font-weight: 700;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .f-btn--primary {
            background: var(--secondary);
            color: #0d1b20 !important;
        }

        .f-btn--primary:hover {
            background: #ff7a26;
        }

        .f-btn--ghost {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff !important;
        }

        .f-btn--ghost:hover {
            border-color: #fff;
        }

        .f-main {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) repeat(2, minmax(0, 0.8fr)) minmax(0, 1.25fr);
            gap: 3rem;
            padding: 4rem 0 3.5rem;
        }

        .f-logo {
            display: inline-flex;
            background: #fff;
            border-radius: 16px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }

        .f-logo img {
            height: 92px;
            width: auto;
            display: block;
        }

        .f-about {
            max-width: 22rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .f-quick {
            display: grid;
            gap: 0.35rem;
            list-style: none;
        }

        .f-quick a {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            min-height: 36px;
            color: #fff;
            font-weight: 600;
        }

        .f-quick a:hover {
            color: #5eead4;
        }

        .f-quick i {
            width: 1.1rem;
            text-align: center;
            color: #5eead4;
        }

        .f-quick small {
            display: block;
            margin-left: 1.8rem;
            color: var(--f-muted);
            font-size: 0.82rem;
        }

        .f-heading {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--f-muted);
            margin-bottom: 1.1rem;
        }

        .f-links {
            list-style: none;
            display: grid;
            gap: 0.15rem;
        }

        .f-links a {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            color: var(--f-text);
            transition: color 0.2s ease;
        }

        .f-links a:hover {
            color: #fff;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .f-place + .f-place {
            margin-top: 1.4rem;
            padding-top: 1.4rem;
            border-top: 1px solid var(--f-line);
        }

        .f-place-code {
            display: block;
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            color: #5eead4;
            margin-bottom: 0.3rem;
        }

        .f-place address {
            font-style: normal;
            line-height: 1.55;
        }

        .f-hours {
            margin-top: 1.4rem;
            font-size: 0.88rem;
            color: var(--f-muted);
        }

        .f-bottom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem 2rem;
            padding: 1.5rem 0;
            border-top: 1px solid var(--f-line);
            font-size: 0.85rem;
            color: var(--f-muted);
        }

        .f-legal {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem 1.25rem;
            list-style: none;
        }

        .f-legal a {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
        }

        .f-legal a:hover {
            color: #fff;
        }

        .social-links {
            display: flex;
            gap: 0.6rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            width: 44px;
            height: 44px;
            border: 1px solid var(--f-line);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .social-links a:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        @media (max-width: 1024px) {
            .f-main {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 2.5rem;
            }

            .f-brand {
                grid-column: 1 / -1;
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                gap: 0 2rem;
                align-items: start;
            }

            .f-logo {
                grid-row: span 2;
            }

            .f-quick {
                display: flex;
                flex-wrap: wrap;
                gap: 0 1.5rem;
            }
        }

        @media (max-width: 600px) {
            .f-wrap {
                padding: 0 1.1rem;
            }

            .f-cta {
                padding: 2.5rem 0;
            }

            .f-cta-actions,
            .f-cta-actions .f-btn {
                width: 100%;
            }

            .f-main {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 2.25rem 1.5rem;
                padding: 3rem 0 2.5rem;
            }

            .f-brand,
            .f-visit {
                grid-column: 1 / -1;
            }

            .f-brand {
                display: block;
            }

            .f-quick {
                display: grid;
            }

            .f-logo img {
                height: 76px;
            }

            .f-bottom {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* WhatsApp Float Button */
        .whatsapp-float {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
            z-index: 999;
            animation: float 3s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 30px rgba(37, 211, 102, 0.6);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Sticky Mobile CTA */
        .sticky-mobile-cta {
            display: none;
        }

        @media (max-width: 768px) {
            .sticky-mobile-cta {
                display: flex;
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 998;
                background: #fff;
                border-top: 1px solid #e2e8f0;
                box-shadow: 0 -4px 20px rgba(0,0,0,.08);
                padding: .75rem 1rem;
                padding-bottom: calc(.75rem + env(safe-area-inset-bottom));
                gap: .75rem;
            }

            .sticky-mobile-cta a {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .5rem;
                padding: .85rem 1rem;
                border-radius: 12px;
                font-weight: 800;
                font-size: .9rem;
                text-decoration: none;
            }

            .sticky-mobile-cta .cta-primary {
                background: rgb(0,127,127);
                color: #fff;
            }

            .sticky-mobile-cta .cta-secondary {
                background: #f1f5f9;
                color: #1e293b;
            }

            body.has-sticky-cta { padding-bottom: 72px; }

            /* The sticky bar already offers WhatsApp on phones, so the
               floating button would only cover page content. */
            body.has-sticky-cta .whatsapp-float {
                display: none;
            }

            .sticky-mobile-cta .cta-whatsapp {
                background: #e7f8ee;
                color: #0b6b3a;
            }

            #cookieNotice {
                right: calc(1rem + 100px) !important;
            }

            body.has-sticky-cta #cookieNotice {
                bottom: calc(72px + 1rem) !important;
            }
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--tertiary);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.1);
        }

        /* Responsive */
        /* 8 nav items + 88px logo overflow below ~1100px, so collapse to the
           hamburger menu there rather than at the phone breakpoint. */
        @media (max-width: 1100px) {
            .nav-menu {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .navbar-container {
                height: 72px;
            }

            .logo-img {
                height: 60px;
            }

            .hero {
                padding: 4rem 0;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .grid-cols-2,
            .grid-cols-3,
            .grid-cols-4 {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 2rem;
            }

            .section {
                padding: 3rem 0;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .btn-primary,
            .btn-secondary,
            .btn-tertiary {
                padding: 0.75rem 1.5rem;
                width: 100%;
                justify-content: center;
            }
        }

        /* Animation for elements */
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    @yield('styles')
</head>
<body class="{{ request()->routeIs('quote') ? '' : 'has-sticky-cta' }} @yield('body_class')">

    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation -->
    <nav class="navbar" aria-label="Main">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="logo" aria-label="Forus Freight home">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" class="logo-img">
            </a>

            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a></li>
                <li><a href="{{ route('services') }}" class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" @if(request()->routeIs('services')) aria-current="page" @endif>Services</a></li>
                <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" @if(request()->routeIs('about')) aria-current="page" @endif>About</a></li>
                <li><a href="{{ route('tracking') }}" class="nav-link {{ request()->routeIs('tracking') ? 'active' : '' }}" @if(request()->routeIs('tracking')) aria-current="page" @endif>Track</a></li>
                <li><a href="{{ route('faq') }}" class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" @if(request()->routeIs('faq')) aria-current="page" @endif>FAQ</a></li>
                <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a></li>
                <li><a href="{{ route('quote') }}" class="nav-btn-outline">Get Quote</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="nav-btn">Dashboard</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="nav-btn">Login</a></li>
                @endauth
            </ul>

            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobileMenu">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a></li>
                <li><a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}" @if(request()->routeIs('services')) aria-current="page" @endif>Services</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}" @if(request()->routeIs('about')) aria-current="page" @endif>About</a></li>
                <li><a href="{{ route('tracking') }}" class="{{ request()->routeIs('tracking') ? 'active' : '' }}" @if(request()->routeIs('tracking')) aria-current="page" @endif>Track</a></li>
                <li><a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}" @if(request()->routeIs('faq')) aria-current="page" @endif>FAQ</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a></li>
                <li><a href="{{ route('quote') }}" class="nav-btn-outline" style="justify-content: center; width: 100%; padding: 0.875rem 1.25rem;">Get Quote</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="nav-btn" style="justify-content: center; width: 100%; padding: 0.875rem 1.25rem;">Dashboard</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="nav-btn" style="justify-content: center; width: 100%; padding: 0.875rem 1.25rem;">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @php
    $footerPage = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first();
    $footer = $footerPage?->sections ?? [];
    $footerWhatsapp = 'https://wa.me/' . ($footer['whatsapp_number'] ?? '260572788685') . '?text=' . urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I need a quote for logistics services');
    $footerPhones = !empty($footer['contact_phones']) ? $footer['contact_phones'] : [['number' => '+260 572 788 685'], ['number' => '+260 766 193059']];
    $footerEmail = $footer['contact_email'] ?? 'info@forusfreight.com';
    // Only real profile URLs get an icon; the CMS defaults are "#" placeholders
    $footerSocials = collect([
        'facebook' => ['fab fa-facebook-f', 'Facebook'],
        'twitter' => ['fab fa-twitter', 'X (Twitter)'],
        'linkedin' => ['fab fa-linkedin-in', 'LinkedIn'],
        'instagram' => ['fab fa-instagram', 'Instagram'],
    ])->filter(fn ($v, $k) => !empty($footer['social_' . $k]) && $footer['social_' . $k] !== '#');
    @endphp
    <footer class="site-footer">
        <div class="f-wrap">
            @unless(request()->routeIs('home', 'quote'))
            <div class="f-cta">
                <div>
                    <h2>Got cargo to move?</h2>
                    <p>Get a clear price and transit time — free, with no obligation.</p>
                </div>
                <div class="f-cta-actions">
                    <a href="{{ route('quote') }}" class="f-btn f-btn--primary">Get a free quote <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="{{ $footerWhatsapp }}" target="_blank" rel="noopener" class="f-btn f-btn--ghost"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
                </div>
            </div>
            @endunless

            <div class="f-main">
                <div class="f-brand">
                    <a href="{{ route('home') }}" class="f-logo" aria-label="Forus Freight home">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" loading="lazy">
                    </a>
                    <p class="f-about">{{ $footer['description'] ?? 'Freight forwarding between Zambia, South Africa, China and the UAE — with customs clearance handled and a free China address for online shoppers.' }}</p>
                    <ul class="f-quick">
                        @foreach($footerPhones as $phone)
                            @if(!empty($phone['number']))
                            <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone['number']) }}"><i class="fas fa-phone" aria-hidden="true"></i>{{ $phone['number'] }}</a></li>
                            @endif
                        @endforeach
                        <li><a href="mailto:{{ $footerEmail }}"><i class="fas fa-envelope" aria-hidden="true"></i>{{ $footerEmail }}</a></li>
                        <li><a href="{{ $footerWhatsapp }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i>Chat on WhatsApp</a></li>
                    </ul>
                    @if($footerSocials->isNotEmpty())
                    <div class="social-links">
                        @foreach($footerSocials as $key => [$icon, $label])
                            <a href="{{ $footer['social_' . $key] }}" target="_blank" rel="noopener" aria-label="Forus Freight on {{ $label }}"><i class="{{ $icon }}" aria-hidden="true"></i></a>
                        @endforeach
                    </div>
                    @endif
                </div>

                <nav aria-labelledby="footerServices">
                    <h2 class="f-heading" id="footerServices">Services</h2>
                    <ul class="f-links">
                        @if(!empty($footer['services_links']))
                            @foreach($footer['services_links'] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['title'] ?? '' }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('services') }}#cross-border">Cross-border freight</a></li>
                            <li><a href="{{ route('services') }}#same-day">Same-day delivery</a></li>
                            <li><a href="{{ route('services') }}#warehousing">Warehousing</a></li>
                            <li><a href="{{ route('services') }}#bulk-cargo">Bulk cargo</a></li>
                            <li><a href="{{ route('home') }}#shop-from-china">Shop from China</a></li>
                        @endif
                    </ul>
                </nav>

                <nav aria-labelledby="footerCompany">
                    <h2 class="f-heading" id="footerCompany">Company</h2>
                    <ul class="f-links">
                        @if(!empty($footer['company_links']))
                            @foreach($footer['company_links'] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['title'] ?? '' }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('about') }}">About us</a></li>
                            <li><a href="{{ route('quote') }}">Get a quote</a></li>
                            <li><a href="{{ route('tracking') }}">Track a shipment</a></li>
                            <li><a href="{{ route('faq') }}">FAQ</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                        @endif
                        @guest
                            <li><a href="{{ route('login') }}">Customer login</a></li>
                        @endguest
                    </ul>
                </nav>

                <div class="f-visit">
                    <h2 class="f-heading">Visit us</h2>
                    <div class="f-place">
                        <span class="f-place-code">LUSAKA &middot; OFFICE</span>
                        <address>{{ $footer['contact_address'] ?? 'Metrolux Plaza, Plot No. 401A/8 Kafue Road' }}<br>{{ $footer['contact_city'] ?? 'Lusaka, Zambia' }}</address>
                    </div>
                    <div class="f-place">
                        <span class="f-place-code">LUSAKA &middot; WAREHOUSE</span>
                        <address>{{ $footer['contact_warehouse_address'] ?? 'Plot No. F/26/397a, Robert ‘B’ Warehouse ‘A’, Off Kafue Road, Makeni' }}<br>{{ $footer['contact_warehouse_city'] ?? 'Lusaka, Zambia' }}</address>
                    </div>
                    <p class="f-hours"><i class="far fa-clock" aria-hidden="true"></i> Mon–Fri, 08:00–17:00</p>
                </div>
            </div>

            <div class="f-bottom">
                <p>{!! str_replace('{year}', date('Y'), $footer['copyright_text'] ?? '&copy; {year} Forus Freight Limited. All rights reserved.') !!}</p>
                <ul class="f-legal" aria-label="Legal">
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('cookie-policy') }}">Cookies</a></li>
                    <li><a href="{{ route('refund-policy') }}">Refunds</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <div role="region" aria-label="Quick contact actions">
        <!-- WhatsApp Float Button -->
        <a href="https://wa.me/{{ $footer['whatsapp_number'] ?? '260572788685' }}?text={{ urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I need a quote for logistics services') }}" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Chat with Forus Freight on WhatsApp">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
        </a>

        @unless(request()->routeIs('quote'))
        <!-- Sticky Mobile CTA -->
        <div class="sticky-mobile-cta">
            <a href="{{ route('quote') }}" class="cta-primary">
                <i class="fas fa-paper-plane" aria-hidden="true"></i> Get a Quote
            </a>
            <a href="https://wa.me/{{ $footer['whatsapp_number'] ?? '260572788685' }}?text={{ urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I need a quote for logistics services') }}" target="_blank" rel="noopener" class="cta-whatsapp">
                <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
            </a>
        </div>
        @endunless
    </div>

    <!-- Cookie Notice -->
    <div id="cookieNotice" role="region" aria-label="Cookie notice" style="display:none; position:fixed; left:1rem; right:1rem; bottom:1rem; z-index:1200; max-width:640px; margin:0 auto; background:#1e293b; color:#e2e8f0; border-radius:16px; padding:1.25rem 1.5rem; box-shadow:0 10px 40px rgba(0,0,0,.3); font-size:.9rem; line-height:1.5;">
        <p style="margin-bottom:1rem;">
            We use only strictly necessary cookies to keep you logged in and to protect our forms — no advertising or analytics tracking. See our
            <a href="{{ route('cookie-policy') }}" style="color:#5eead4; font-weight:700;">Cookie Policy</a>
            for details.
        </p>
        <div style="display:flex; justify-content:flex-end; gap:.75rem;">
            <button type="button" id="cookieNoticeAccept" style="background:rgb(0,127,127); color:#fff; border:none; border-radius:10px; padding:.75rem 1.5rem; min-height:44px; font-weight:700; cursor:pointer;">Got it</button>
        </div>
    </div>

    <!-- Welcome / Sign-up Popup (guests only; never on pages where the visitor is already taking action) -->
    @if(auth()->guest() && ! request()->routeIs('quote', 'contact', 'track', 'track.check', 'tracking', 'tracking.*', 'register', 'login', 'password.*'))
    <style>
        #welcomeModal.wm-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(7, 20, 24, 0.62);
            backdrop-filter: blur(6px);
            animation: wmFade 0.3s ease;
        }

        #welcomeModal.wm-overlay[hidden] { display: none; }

        @keyframes wmFade { from { opacity: 0; } to { opacity: 1; } }

        @keyframes wmRise {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to { opacity: 1; transform: none; }
        }

        .wm-modal {
            position: relative;
            width: 100%;
            max-width: 920px;
            max-height: calc(100vh - 2.5rem);
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            border-radius: 26px;
            overflow: hidden;
            background: #0d1b20;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
            animation: wmRise 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            color: #fff;
            text-align: left;
        }

        .wm-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 5;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: rgba(13, 27, 32, 0.08);
            color: #0d1b20;
            font-size: 1.05rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .wm-close:hover { background: rgba(13, 27, 32, 0.16); }

        /* ---- Left: pitch, steps, CTA ---- */
        .wm-copy {
            padding: 2.5rem 2.25rem 2.25rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .wm-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 0.74rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #5eead4;
        }

        .wm-eyebrow::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff6200;
        }

        .wm-title {
            font-family: 'Archivo', 'Inter', sans-serif;
            font-stretch: 110%;
            font-weight: 800;
            font-size: 2.15rem;
            line-height: 1.05;
            letter-spacing: -0.02em;
            margin: 0.9rem 0 0.85rem;
            color: #fff;
        }

        .wm-title em {
            font-style: normal;
            color: #ff8a3d;
        }

        .wm-lead {
            color: #c4d1d5;
            font-size: 0.98rem;
            line-height: 1.55;
            margin: 0 0 1.5rem;
        }

        .wm-steps {
            list-style: none;
            margin: 0 0 1.75rem;
            padding: 0;
            display: grid;
            gap: 0.5rem;
        }

        .wm-step {
            position: relative;
            width: 100%;
            display: grid;
            grid-template-columns: 2.25rem 1fr;
            gap: 0.25rem 0.75rem;
            align-items: start;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            color: #c4d1d5;
            text-align: left;
            font: inherit;
            cursor: pointer;
            overflow: hidden;
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }

        .wm-step:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .wm-step[aria-selected="true"] {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(94, 234, 212, 0.45);
            color: #fff;
        }

        .wm-step-num {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 0.8rem;
            color: #ff8a3d;
            padding-top: 0.1rem;
        }

        .wm-step-title {
            font-weight: 700;
            font-size: 0.98rem;
            color: inherit;
        }

        .wm-step-desc {
            grid-column: 2;
            font-size: 0.85rem;
            line-height: 1.45;
            color: #9fb3b9;
            display: none;
        }

        .wm-step[aria-selected="true"] .wm-step-desc {
            display: block;
        }

        .wm-step-bar {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background: linear-gradient(90deg, #2dd4bf, #ff6200);
        }

        .wm-cta {
            margin-top: auto;
        }

        .wm-btn-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            width: 100%;
            min-height: 54px;
            padding: 0 1.25rem;
            border-radius: 14px;
            background: #ff6200;
            color: #0d1b20 !important;
            font-weight: 800;
            font-size: 1.02rem;
            text-decoration: none;
            box-shadow: 0 12px 30px rgba(255, 98, 0, 0.35);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .wm-btn-main:hover {
            background: #ff7a26;
            transform: translateY(-1px);
        }

        .wm-btn-main .fa-arrow-right { transition: transform 0.2s ease; }
        .wm-btn-main:hover .fa-arrow-right { transform: translateX(3px); }

        .wm-cta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.85rem;
            font-size: 0.85rem;
        }

        .wm-login {
            color: #5eead4 !important;
            font-weight: 600;
            text-decoration: none;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
        }

        .wm-login:hover { text-decoration: underline; }

        .wm-skip {
            background: none;
            border: none;
            color: #8fa6ad;
            font: inherit;
            font-size: 0.85rem;
            cursor: pointer;
            min-height: 36px;
            padding: 0;
        }

        .wm-skip:hover { color: #fff; }

        .wm-fine {
            margin-top: 0.6rem;
            font-size: 0.78rem;
            color: #8fa6ad;
        }

        .wm-fine i { color: #5eead4; margin-right: 0.3rem; }

        /* ---- Right: real screenshots ---- */
        .wm-stage {
            position: relative;
            background:
                radial-gradient(circle at 80% 15%, rgba(45, 212, 191, 0.18), transparent 50%),
                #f5f4ef;
            min-height: 520px;
            overflow: hidden;
        }

        .wm-shot {
            position: absolute;
            inset: 4.5rem 2rem 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transform: translateY(14px) scale(0.98);
            transition: opacity 0.4s ease, transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), visibility 0s linear 0.4s;
            pointer-events: none;
        }

        .wm-shot.is-active {
            opacity: 1;
            visibility: visible;
            transform: none;
            transition-delay: 0s;
        }

        .wm-device {
            position: relative;
            width: 100%;
        }

        .wm-frame {
            position: relative;
            width: 100%;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 30px 60px rgba(13, 27, 32, 0.22), 0 0 0 1px rgba(13, 27, 32, 0.06);
            overflow: hidden;
        }

        .wm-frame-bar {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 0.8rem;
            background: #eef0ee;
            border-bottom: 1px solid rgba(13, 27, 32, 0.06);
        }

        .wm-frame-bar span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #d3d8d6;
        }

        .wm-frame-bar b {
            margin-left: 0.6rem;
            flex: 1;
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-size: 0.68rem;
            font-weight: 400;
            color: #56666c;
            background: #fff;
            border-radius: 6px;
            padding: 0.2rem 0.6rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wm-frame img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 360px;
            object-fit: cover;
            object-position: top;
        }

        /* Hide the personal address lines: they unlock after sign-up */
        .wm-lock {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 50%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 1.1rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.55) 30%);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .wm-lock span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #0d1b20;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
        }

        .wm-lock i { color: #ff8a3d; }

        .wm-chip {
            position: absolute;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #fff;
            color: #0d1b20;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.55rem 0.85rem;
            border-radius: 12px;
            box-shadow: 0 14px 30px rgba(13, 27, 32, 0.18);
            white-space: nowrap;
        }

        .wm-chip i {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            background: #e6f6f4;
            color: #007f7f;
        }

        .wm-chip--tl { top: -16px; left: -14px; }
        .wm-chip--br { bottom: -16px; right: -14px; }

        @media (max-width: 760px) {
            #welcomeModal.wm-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .wm-modal {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
                max-height: 94vh;
                border-radius: 22px 22px 0 0;
                animation-name: wmSheet;
            }

            @keyframes wmSheet {
                from { transform: translateY(40px); opacity: 0; }
                to { transform: none; opacity: 1; }
            }

            .wm-stage {
                order: -1;
                min-height: 0;
                height: 205px;
            }

            .wm-shot {
                inset: 3.25rem 1.25rem 0.75rem;
                align-items: flex-start;
            }

            .wm-frame img {
                max-height: 135px;
            }

            .wm-chip {
                display: none;
            }

            .wm-copy {
                padding: 1.4rem 1.25rem 1.25rem;
            }

            .wm-title {
                font-size: 1.6rem;
                margin: 0.6rem 0 0.5rem;
            }

            .wm-lead {
                font-size: 0.92rem;
                margin-bottom: 1rem;
            }

            .wm-steps {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.4rem;
                margin-bottom: 1.1rem;
            }

            .wm-step {
                grid-template-columns: 1fr;
                padding: 0.6rem 0.55rem;
                gap: 0.1rem;
            }

            .wm-step-title {
                font-size: 0.78rem;
                line-height: 1.25;
            }

            .wm-step-desc,
            .wm-step[aria-selected="true"] .wm-step-desc {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .wm-shot { transition: none; }
        }
    </style>

    <div id="welcomeModal" class="wm-overlay" hidden>
        <div class="wm-modal" role="dialog" aria-modal="true" aria-labelledby="wmTitle" aria-describedby="wmLead">
            <button type="button" class="wm-close" id="wmClose" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>

            <div class="wm-copy">
                <span class="wm-eyebrow">Free for online shoppers</span>
                <h2 class="wm-title" id="wmTitle">Your own address in China. <em>Free.</em></h2>
                <p class="wm-lead" id="wmLead">Shop on Alibaba, Amazon, Shein, Temu and more. We receive your parcels, combine them and ship them to Lusaka.</p>

                <div class="wm-steps" role="tablist" aria-label="How it works">
                    <button type="button" class="wm-step" role="tab" id="wmTab0" aria-controls="wmShot0" aria-selected="true">
                        <span class="wm-step-num">01</span>
                        <span class="wm-step-title">Create your free account</span>
                        <span class="wm-step-desc">About two minutes. Verify your email and phone.</span>
                        <span class="wm-step-bar" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="wm-step" role="tab" id="wmTab1" aria-controls="wmShot1" aria-selected="false" tabindex="-1">
                        <span class="wm-step-num">02</span>
                        <span class="wm-step-title">Get your China address</span>
                        <span class="wm-step-desc">Use it as the delivery address when you check out.</span>
                        <span class="wm-step-bar" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="wm-step" role="tab" id="wmTab2" aria-controls="wmShot2" aria-selected="false" tabindex="-1">
                        <span class="wm-step-num">03</span>
                        <span class="wm-step-title">Track it to Lusaka</span>
                        <span class="wm-step-desc">Follow every stage online, with email updates.</span>
                        <span class="wm-step-bar" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="wm-cta">
                    <a href="{{ route('register') }}" class="wm-btn-main">Create my free account <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <div class="wm-cta-row">
                        <a href="{{ route('login') }}" class="wm-login">I already have an account</a>
                        <button type="button" id="wmSkip" class="wm-skip">Not now</button>
                    </div>
                    <p class="wm-fine"><i class="fas fa-circle-check" aria-hidden="true"></i>Free to join &middot; No card needed</p>
                </div>
            </div>

            <div class="wm-stage">
                <div class="wm-shot is-active" id="wmShot0" role="tabpanel" aria-labelledby="wmTab0">
                    <div class="wm-device">
                        <div class="wm-frame">
                            <div class="wm-frame-bar" aria-hidden="true"><span></span><span></span><span></span><b>Create account</b></div>
                            <img data-src="{{ asset('images/tutorial/popup-signup.webp') }}" alt="The Forus Freight sign-up form: full name, email, mobile number and password" width="860" height="880" decoding="async">
                        </div>
                        <span class="wm-chip wm-chip--br"><i class="fas fa-stopwatch" aria-hidden="true"></i>About 2 minutes</span>
                    </div>
                </div>
                <div class="wm-shot" id="wmShot1" role="tabpanel" aria-labelledby="wmTab1">
                    <div class="wm-device">
                        <div class="wm-frame">
                            <div class="wm-frame-bar" aria-hidden="true"><span></span><span></span><span></span><b>Your account &rsaquo; Getting started</b></div>
                            <img data-src="{{ asset('images/tutorial/popup-china-address.webp') }}" alt="Your personal China warehouse address as shown in your Forus Freight account" width="960" height="810" decoding="async">
                            <div class="wm-lock"><span><i class="fas fa-lock" aria-hidden="true"></i>Unlocks when you sign up</span></div>
                        </div>
                        <span class="wm-chip wm-chip--br"><i class="fas fa-location-dot" aria-hidden="true"></i>Foshan, China</span>
                    </div>
                </div>
                <div class="wm-shot" id="wmShot2" role="tabpanel" aria-labelledby="wmTab2">
                    <div class="wm-device">
                        <div class="wm-frame">
                            <div class="wm-frame-bar" aria-hidden="true"><span></span><span></span><span></span><b>Track shipment</b></div>
                            <img data-src="{{ asset('images/tutorial/popup-tracking.webp') }}" alt="A shipment timeline: received at China warehouse, consolidated, departed China, in transit to Zambia" width="844" height="980" decoding="async">
                        </div>
                        <span class="wm-chip wm-chip--br"><i class="fas fa-envelope" aria-hidden="true"></i>Email at every step</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const overlay = document.getElementById('welcomeModal');
            if (!overlay) return;
            const KEY = 'forus_welcome_seen';
            try { if (sessionStorage.getItem(KEY)) return; } catch (e) {}

            const modal = overlay.querySelector('.wm-modal');
            const tabs = Array.from(overlay.querySelectorAll('.wm-step'));
            const shots = Array.from(overlay.querySelectorAll('.wm-shot'));
            const closeBtn = document.getElementById('wmClose');
            const skipBtn = document.getElementById('wmSkip');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            const DUR = 5500;
            let idx = 0, timer = null, paused = false, lastFocus = null;

            function paint() {
                tabs.forEach((t, i) => {
                    const on = i === idx;
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                    t.tabIndex = on ? 0 : -1;
                    const bar = t.querySelector('.wm-step-bar');
                    bar.style.transition = 'none';
                    bar.style.width = '0%';
                    if (on && !paused && !reduceMotion.matches) {
                        requestAnimationFrame(() => requestAnimationFrame(() => {
                            bar.style.transition = 'width ' + DUR + 'ms linear';
                            bar.style.width = '100%';
                        }));
                    }
                });
                shots.forEach((s, i) => s.classList.toggle('is-active', i === idx));
            }
            function go(i) { idx = (i + tabs.length) % tabs.length; paint(); restart(); }
            function restart() {
                clearInterval(timer);
                if (paused || reduceMotion.matches) return;
                timer = setInterval(() => go(idx + 1), DUR);
            }
            function setPaused(p) { paused = p; paint(); restart(); }

            tabs.forEach((t, i) => {
                t.addEventListener('click', () => go(i));
                t.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); go(i + 1); tabs[idx].focus(); }
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); go(i - 1); tabs[idx].focus(); }
                });
            });
            // Hold the current step while the visitor is reading or interacting
            modal.addEventListener('mouseenter', () => setPaused(true));
            modal.addEventListener('mouseleave', () => setPaused(false));
            modal.addEventListener('focusin', () => { if (!paused) setPaused(true); });

            function show() {
                // Screenshots are only downloaded once the popup is actually shown
                overlay.querySelectorAll('img[data-src]').forEach(img => {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                });
                lastFocus = document.activeElement;
                overlay.hidden = false;
                paint(); restart();
                document.body.style.overflow = 'hidden';
                closeBtn.focus();
            }
            function hide() {
                if (overlay.hidden) return;
                clearInterval(timer); overlay.hidden = true;
                document.body.style.overflow = '';
                try { sessionStorage.setItem(KEY, '1'); } catch (e) {}
                if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
            }

            closeBtn.addEventListener('click', hide);
            skipBtn.addEventListener('click', hide);
            overlay.addEventListener('click', (e) => { if (e.target === overlay) hide(); });
            document.addEventListener('keydown', (e) => {
                if (overlay.hidden) return;
                if (e.key === 'Escape') { hide(); return; }
                if (e.key !== 'Tab') return;
                // Keep keyboard focus inside the dialog while it is open
                const f = Array.from(modal.querySelectorAll('a[href], button:not([disabled])'))
                    .filter(el => el.offsetParent !== null && el.tabIndex !== -1);
                if (!f.length) return;
                const first = f[0], last = f[f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            });
            document.addEventListener('visibilitychange', () => {
                if (document.hidden || overlay.hidden) clearInterval(timer); else restart();
            });

            // Let visitors read the page first: open after a delay (10s on the
            // homepage, 25s elsewhere), or once they've scrolled halfway down,
            // whichever happens first.
            let shown = false;
            function maybeShow() {
                if (shown) return;
                shown = true;
                window.removeEventListener('scroll', onScroll);
                show();
            }
            function onScroll() {
                const max = document.documentElement.scrollHeight - window.innerHeight;
                if (max > 0 && window.scrollY / max >= 0.5) maybeShow();
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            setTimeout(maybeShow, {{ request()->routeIs('home') ? 10000 : 25000 }});

            // Handy for previewing the design: add #preview-welcome to any URL
            if (location.hash === '#preview-welcome') { shown = true; show(); }
        })();
    </script>
    @endif

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
                // Change icon
                const icon = this.querySelector('i');
                const isOpen = mobileMenu.classList.contains('active');
                icon.className = isOpen ? 'fas fa-times' : 'fas fa-bars';
                mobileMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                icon.setAttribute('aria-hidden', 'true');
            });

            function closeMobileMenu() {
                mobileMenu.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.className = 'fas fa-bars';
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                    closeMobileMenu();
                    mobileMenuBtn.focus();
                }
            });

            mobileMenu.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', closeMobileMenu);
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    mobileMenu.classList.remove('active');
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.className = 'fas fa-bars';
                    icon.setAttribute('aria-hidden', 'true');
                    mobileMenuBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                if (this.getAttribute('href') !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.getBoundingClientRect().top + window.scrollY - (document.querySelector('.navbar')?.offsetHeight || 80),
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });

        // Add animation classes to elements as they come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe elements with data-animate attribute
        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });

        // Cookie notice
        (function () {
            function getCookie(name) {
                return document.cookie.split('; ').find(row => row.startsWith(name + '='));
            }

            const notice = document.getElementById('cookieNotice');
            const acceptBtn = document.getElementById('cookieNoticeAccept');

            if (notice && acceptBtn && !getCookie('cookie_consent')) {
                notice.style.display = 'block';
            }

            acceptBtn?.addEventListener('click', function () {
                const expires = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
                document.cookie = 'cookie_consent=accepted; expires=' + expires + '; path=/; SameSite=Lax';
                notice.style.display = 'none';
            });
        })();

        // Same pattern as layouts/dashboard.blade.php: disable + spinner any
        // plain form submit button so a slow request doesn't look like a dead
        // click. Pages with their own AJAX handling (contact, quote) opt out
        // via data-no-loader since they already manage button state themselves.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (e.defaultPrevented) return;
            if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) return;
            if (form.dataset.noLoader !== undefined) return;

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
                if (btn.disabled) return;
                if (btn.tagName === 'BUTTON') {
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
                } else {
                    btn.dataset.originalValue = btn.value;
                    btn.value = 'Processing...';
                }
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
