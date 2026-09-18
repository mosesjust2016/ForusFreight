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

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Forus Freight">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Forus Freight - Global Logistics Solutions')">
    <meta property="og:description" content="@yield('meta_description', 'Forus Freight Limited is a Zambian logistics company providing road, air, and sea freight, customs brokerage, and warehousing across Zambia and the SADC region.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Forus Freight - Global Logistics Solutions')">
    <meta name="twitter:description" content="@yield('meta_description', 'Forus Freight Limited is a Zambian logistics company providing road, air, and sea freight, customs brokerage, and warehousing across Zambia and the SADC region.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

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
        "email": "info@forusfl.co.zm",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "METROLUX PLAZA, Plot No. 401A/8 Kafure Road",
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        .logo-icon {
            width: 0x;
            height: 0px;
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
            top: 80px;
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
            color: rgba(255, 255, 255, 0.8);
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
        .footer {
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 5rem 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: white;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-contact {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }

        .footer-contact i {
            color: var(--primary);
            font-size: 1.25rem;
            margin-top: 0.25rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
            color: #94a3b8;
        }

        .footer-bottom a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-bottom a:hover {
            color: white;
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

            body.has-sticky-cta .whatsapp-float {
                bottom: calc(72px + 1rem);
                right: 1rem;
                width: 52px;
                height: 52px;
                font-size: 1.6rem;
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
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .grid-cols-2,
            .grid-cols-3,
            .grid-cols-4 {
                grid-template-columns: 1fr;
            }

            .footer-grid {
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
    </style>

    @yield('styles')
</head>
<body class="{{ request()->routeIs('quote') ? '' : 'has-sticky-cta' }}">

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" style="height: 88px; width: auto; display: block;">
            </a>

            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('services') }}" class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}">Services</a></li>
                <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ route('tracking') }}" class="nav-link {{ request()->routeIs('tracking') ? 'active' : '' }}">Track</a></li>
                <li><a href="{{ route('faq') }}" class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a></li>
                <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
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
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">Services</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ route('tracking') }}" class="{{ request()->routeIs('tracking') ? 'active' : '' }}">Track</a></li>
                <li><a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
                <li><a href="{{ route('quote') }}" class="nav-btn-outline" style="justify-content: center;">Get Quote</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="nav-btn" style="justify-content: center;">Dashboard</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="nav-btn" style="justify-content: center;">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @php
    $footerPage = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first();
    $footer = $footerPage?->sections ?? [];
    @endphp
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" style="height: 56px; width: auto; display: block;">
                    </div>
                    <p style="color: #94a3b8; margin-bottom: 1.5rem;">{{ $footer['description'] ?? 'Fast, reliable & affordable logistics solutions across Zambia and the SADC region.' }}</p>
                    <div class="social-links">
                        @if(!empty($footer['social_facebook']))<a href="{{ $footer['social_facebook'] }}" aria-label="Forus Freight on Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                        @if(!empty($footer['social_twitter']))<a href="{{ $footer['social_twitter'] }}" aria-label="Forus Freight on Twitter"><i class="fab fa-twitter"></i></a>@endif
                        @if(!empty($footer['social_linkedin']))<a href="{{ $footer['social_linkedin'] }}" aria-label="Forus Freight on LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                        @if(!empty($footer['social_instagram']))<a href="{{ $footer['social_instagram'] }}" aria-label="Forus Freight on Instagram"><i class="fab fa-instagram"></i></a>@endif
                    </div>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="footer-title">Our Services</h3>
                    <ul class="footer-links">
                        @if(!empty($footer['services_links']))
                            @foreach($footer['services_links'] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}"><i class="fas fa-arrow-right"></i> {{ $link['title'] ?? '' }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('services') }}#same-day"><i class="fas fa-arrow-right"></i> Same-Day Delivery</a></li>
                            <li><a href="{{ route('services') }}#cross-border"><i class="fas fa-arrow-right"></i> Cross-Border Shipping</a></li>
                            <li><a href="{{ route('services') }}#warehousing"><i class="fas fa-arrow-right"></i> Warehousing</a></li>
                            <li><a href="{{ route('services') }}#bulk-cargo"><i class="fas fa-arrow-right"></i> Bulk Cargo</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="footer-title">Company</h3>
                    <ul class="footer-links">
                        @if(!empty($footer['company_links']))
                            @foreach($footer['company_links'] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}"><i class="fas fa-arrow-right"></i> {{ $link['title'] ?? '' }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('about') }}"><i class="fas fa-arrow-right"></i> About Us</a></li>
                            <li><a href="{{ route('services') }}"><i class="fas fa-arrow-right"></i> Services</a></li>
                            <li><a href="{{ route('quote') }}"><i class="fas fa-arrow-right"></i> Get Quote</a></li>
                            <li><a href="{{ route('tracking') }}"><i class="fas fa-arrow-right"></i> Track Shipment</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="footer-title">Get in Touch</h3>
                    <div class="footer-contact">
                        <i class="fas fa-phone"></i>
                        <div>
                            @if(!empty($footer['contact_phones']))
                                @foreach($footer['contact_phones'] as $phone)
                                <span style="display: block; font-weight: 600;">{{ $phone['number'] ?? '' }}</span>
                                @endforeach
                            @else
                                <span style="display: block; font-weight: 600;">+260 572 788 685</span>
                                <span style="display: block; font-weight: 600;">+260 766 193059</span>
                            @endif
                            <span style="font-size: 0.875rem; color: #cbd5e1;">{{ $footer['contact_support_label'] ?? '24/7 Support' }}</span>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">{{ $footer['contact_email'] ?? 'info@forusfl.co.zm' }}</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">{{ $footer['contact_email_label'] ?? 'Email Us' }}</span>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <span style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #cbd5e1;">Office Address</span>
                            <span style="display: block; font-weight: 600;">{{ $footer['contact_address'] ?? 'Forus Freight Ltd METROLUX PLAZA Plot No. 401A/8 Kafure Road' }}</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">{{ $footer['contact_city'] ?? 'Lusaka, Zambia' }}</span>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-warehouse"></i>
                        <div>
                            <span style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #cbd5e1;">Warehouse Address</span>
                            <span style="display: block; font-weight: 600;">{{ $footer['contact_warehouse_address'] ?? 'Plot No. F/26/397a, ROBERT \'B\' Warehouse \'A\' Off Kafue Road, Makeni Area' }}</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">{{ $footer['contact_warehouse_city'] ?? 'Lusaka, Zambia' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>@php echo str_replace('{year}', date('Y'), $footer['copyright_text'] ?? '© {year} Forus Freight. All rights reserved.'); @endphp | <a href="{{ route('privacy') }}">Privacy Policy</a> | <a href="{{ route('cookie-policy') }}">Cookie Policy</a> | <a href="{{ route('refund-policy') }}">Refund Policy</a> | <a href="{{ route('terms') }}">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <div role="region" aria-label="Quick contact actions">
        <!-- WhatsApp Float Button -->
        <a href="https://wa.me/{{ $footer['whatsapp_number'] ?? '260572788685' }}?text={{ urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I need a quote for logistics services') }}" target="_blank" class="whatsapp-float" aria-label="Chat with Forus Freight on WhatsApp">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
        </a>

        @unless(request()->routeIs('quote'))
        <!-- Sticky Mobile CTA -->
        <div class="sticky-mobile-cta">
            <a href="{{ route('quote') }}" class="cta-primary">
                <i class="fas fa-paper-plane" aria-hidden="true"></i> Get a Quote
            </a>
            <a href="tel:{{ $footer['whatsapp_number'] ?? '260572788685' }}" class="cta-secondary" aria-label="Call Forus Freight">
                <i class="fas fa-phone" aria-hidden="true"></i> Call Us
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
            <button type="button" id="cookieNoticeAccept" style="background:rgb(0,127,127); color:#fff; border:none; border-radius:10px; padding:.6rem 1.25rem; font-weight:700; cursor:pointer;">Got it</button>
        </div>
    </div>

    <!-- Welcome / Sign-up Popup (guests only) -->
    @guest
    <style>
        #welcomeModal.wm-overlay {
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(3px);
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; animation: wmFade 0.35s ease;
        }
        #welcomeModal.wm-overlay[hidden] { display: none; }
        @keyframes wmFade { from { opacity: 0; } to { opacity: 1; } }
        .wm-modal {
            background: #fff; color: #1e293b; border-radius: 24px;
            max-width: 560px; width: 100%; max-height: 92vh; overflow: auto;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.45);
            animation: wmPop 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        @keyframes wmPop {
            from { transform: translateY(26px) scale(0.96); opacity: 0; }
            to { transform: none; opacity: 1; }
        }
        .wm-close {
            position: absolute; top: 0.9rem; right: 0.9rem; z-index: 5;
            width: 34px; height: 34px; border: none; border-radius: 50%;
            background: #f1f5f9; color: #475569; font-size: 1rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .wm-close:hover { background: #e2e8f0; }
        .wm-head { display: flex; align-items: center; gap: 0.8rem; padding: 1.4rem 1.6rem 0; }
        .wm-head img { height: 46px; width: auto; }
        .wm-head .wm-brand { font-weight: 800; font-size: 1rem; color: #1e293b; }
        .wm-badge {
            display: inline-block; margin-top: 0.3rem;
            background: linear-gradient(135deg, #ff6200, #ff9a4d);
            color: #fff; font-size: 0.68rem; font-weight: 800;
            padding: 0.3rem 0.7rem; border-radius: 999px;
        }
        .wm-tutorial { margin: 1.2rem 1.6rem 0; }
        .wm-track { position: relative; min-height: 300px; }
        .wm-slide {
            position: absolute; inset: 0; display: flex; gap: 1rem; align-items: flex-start;
            opacity: 0; transform: translateX(24px); pointer-events: none;
            transition: opacity 0.45s ease, transform 0.45s ease;
        }
        .wm-slide.active { opacity: 1; transform: none; pointer-events: auto; }
        .wm-slide--shot { flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .wm-slide--shot .wm-head-row { display: flex; align-items: flex-start; gap: 0.8rem; }
        .wm-shot {
            display: block; width: 100%; border-radius: 14px;
            border: 1px solid #e2e8f0; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.10);
            max-height: 190px; object-fit: cover; object-position: center top;
            background: #f8fafc; cursor: zoom-in;
        }
        .wm-ico {
            width: 46px; height: 46px; border-radius: 14px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; color: #fff;
        }
        .wm-ico.i1 { background: #007f7f; }
        .wm-ico.i2 { background: #ff6200; }
        .wm-ico.i3 { background: #0f172a; }
        .wm-slide h4 { margin: 0 0 0.35rem; font-size: 0.98rem; font-weight: 800; color: #1e293b; }
        .wm-slide p { margin: 0; font-size: 0.84rem; color: #64748b; line-height: 1.55; }
        .wm-addr {
            margin-top: 0.6rem; background: #f8fafc; border: 1px dashed #cbd5e1;
            border-radius: 10px; padding: 0.55rem 0.7rem; font-size: 0.76rem;
            color: #475569; font-family: ui-monospace, monospace; line-height: 1.5;
        }
        .wm-nav { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 1rem; }
        .wm-dots { display: flex; gap: 0.4rem; }
        .wm-dot { width: 8px; height: 8px; border-radius: 999px; border: none; background: #dbe0e8; cursor: pointer; padding: 0; transition: all 0.3s; }
        .wm-dot.active { width: 24px; background: #ff6200; }
        .wm-arrow {
            width: 30px; height: 30px; border-radius: 50%; border: 1px solid #e2e8f0;
            background: #fff; color: #007f7f; cursor: pointer; padding: 0; font-size: 0.7rem;
        }
        .wm-arrow:hover { background: #007f7f; color: #fff; }
        .wm-progress { height: 4px; background: #f1f5f9; border-radius: 4px; margin: 0.9rem 1.6rem 0; overflow: hidden; }
        .wm-progress span { display: block; height: 100%; width: 0%; background: linear-gradient(90deg, #007f7f, #ff6200); }
        .wm-cta {
            background: #f8fafc; border-top: 1px solid #eef2f7;
            border-radius: 0 0 24px 24px; padding: 1.2rem 1.6rem 1.4rem; margin-top: 1.2rem; text-align: center;
        }
        .wm-cta p { margin: 0 0 0.9rem; font-size: 0.85rem; color: #475569; line-height: 1.5; }
        .wm-cta p strong { color: #ff6200; }
        .wm-btns { display: flex; gap: 0.8rem; }
        .wm-btn-ca {
            flex: 1.4; background: #007f7f; color: #fff; border: none; border-radius: 14px;
            padding: 0.9rem 1rem; font-weight: 800; font-size: 0.92rem; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
            gap: 0.5rem; box-shadow: 0 6px 18px rgba(0, 127, 127, 0.3); transition: all 0.25s;
        }
        .wm-btn-ca:hover { background: #006666; transform: translateY(-2px); }
        .wm-btn-lo {
            flex: 1; background: #fff; border: 2px solid #007f7f; color: #007f7f;
            border-radius: 14px; padding: 0.85rem 1rem; font-weight: 800; font-size: 0.92rem;
            cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
            justify-content: center; gap: 0.5rem; transition: all 0.25s;
        }
        .wm-btn-lo:hover { background: #007f7f; color: #fff; }
        .wm-skip { display: block; margin: 1rem auto 0; background: none; border: none; color: #94a3b8; font-size: 0.78rem; cursor: pointer; text-decoration: underline; }
        @media (max-width: 480px) {
            .wm-modal { max-width: 100%; }
            .wm-head { padding: 1.1rem 1.2rem 0; }
            .wm-tutorial { margin: 1rem 1.2rem 0; }
            .wm-track { min-height: 250px; }
            .wm-slide { flex-direction: column; gap: 0.6rem; }
            .wm-slide--shot { flex-direction: column; }
            .wm-shot { max-height: 140px; }
            .wm-btns { flex-direction: column; }
        }
    </style>
    <div id="welcomeModal" class="wm-overlay" hidden>
        <div class="wm-modal" role="dialog" aria-modal="true" aria-labelledby="wmTitle">
            <button type="button" class="wm-close" id="wmClose" aria-label="Close"><i class="fas fa-times"></i></button>
            <div class="wm-head">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
                <div>
                    <div class="wm-brand" id="wmTitle">Welcome to Forus Freight</div>
                    <span class="wm-badge">Get your FREE China warehouse address</span>
                </div>
            </div>
            <div class="wm-tutorial">
                <div class="wm-track" id="wmTrack">
                    <div class="wm-slide active wm-slide--shot">
                        <div class="wm-head-row">
                            <div class="wm-ico i1"><i class="fas fa-user-plus"></i></div>
                            <div>
                                <h4>1 &middot; Create your free account</h4>
                                <p>In less than 2 minutes you unlock your personal China warehouse address — our dedicated forwarding hub that lets you buy from China stores.</p>
                            </div>
                        </div>
                        <img class="wm-shot" src="{{ asset('images/tutorial/register-card-top.png') }}"
                            alt="Screenshot of the Forus Freight account creation form"
                            onclick="this.style.maxHeight='none';this.style.cursor='zoom-out';" title="Click to see the full form">
                    </div>
                    <div class="wm-slide wm-slide--shot">
                        <div class="wm-head-row">
                            <div class="wm-ico i2"><i class="fas fa-cart-shopping"></i></div>
                            <div>
                                <h4>2 &middot; Shop &amp; ship to your China address</h4>
                                <p>Buy on Alibaba, eBay, Amazon, Shein or Temu and use your China address as the delivery point. We receive every parcel — no forwarding apps needed.</p>
                            </div>
                        </div>
                        <img class="wm-shot" src="{{ asset('images/tutorial/china-address-box.png') }}"
                            alt="Screenshot of your Forus Freight China warehouse address"
                            onclick="this.style.maxHeight='none';this.style.cursor='zoom-out';" title="Click to see the full address">
                    </div>
                    <div class="wm-slide wm-slide--shot">
                        <div class="wm-head-row">
                            <div class="wm-ico i3"><i class="fas fa-truck-fast"></i></div>
                            <div>
                                <h4>3 &middot; We consolidate, ship &amp; you track</h4>
                                <p>We combine your parcels, route them safely to Zambia, and give you live tracking until your goods arrive at our Lusaka warehouse for collection or delivery.</p>
                            </div>
                        </div>
                        <img class="wm-shot" src="{{ asset('images/tutorial/tracking-form.png') }}"
                            alt="Screenshot of the Forus Freight live tracking screen"
                            onclick="this.style.maxHeight='none';this.style.cursor='zoom-out';" title="Click to see the full screen">
                    </div>
                </div>
                <div class="wm-nav">
                    <button type="button" class="wm-arrow" id="wmPrev" aria-label="Previous step"><i class="fas fa-chevron-left"></i></button>
                    <div class="wm-dots" id="wmDots"></div>
                    <button type="button" class="wm-arrow" id="wmNext" aria-label="Next step"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="wm-progress"><span id="wmBar"></span></div>
            </div>
            <div class="wm-cta">
                <p>Create an account and <strong>ship your first parcel from China</strong> to Lusaka at unbeatable rates.</p>
                <div class="wm-btns">
                    <a href="{{ route('register') }}" class="wm-btn-ca"><i class="fas fa-user-plus"></i> Create Free Account</a>
                    <a href="{{ route('login') }}" class="wm-btn-lo">I have an account</a>
                </div>
                <button type="button" id="wmSkip" class="wm-skip">Maybe later — keep browsing</button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const overlay = document.getElementById('welcomeModal');
            if (!overlay) return;
            const KEY = 'forus_welcome_seen';
            try { if (sessionStorage.getItem(KEY)) return; } catch (e) {}

            const slides = Array.from(document.getElementById('wmTrack').children);
            const dotsWrap = document.getElementById('wmDots');
            const bar = document.getElementById('wmBar');
            const prev = document.getElementById('wmPrev');
            const next = document.getElementById('wmNext');
            const closeBtn = document.getElementById('wmClose');
            const skipBtn = document.getElementById('wmSkip');
            const DUR = 6000;
            let idx = 0, timer = null;

            const dots = slides.map((_, i) => {
                const d = document.createElement('button');
                d.type = 'button';
                d.className = 'wm-dot' + (i === 0 ? ' active' : '');
                d.setAttribute('aria-label', 'Step ' + (i + 1));
                d.addEventListener('click', () => go(i));
                dotsWrap.appendChild(d);
                return d;
            });

            function paint() {
                slides.forEach((s, i) => s.classList.toggle('active', i === idx));
                dots.forEach((d, i) => d.classList.toggle('active', i === idx));
                bar.style.transition = 'none';
                bar.style.width = '0%';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    bar.style.transition = 'width ' + DUR + 'ms linear';
                    bar.style.width = '100%';
                }));
            }
            function go(i) { idx = (i + slides.length) % slides.length; paint(); restart(); }
            function restart() { clearInterval(timer); timer = setInterval(() => go(idx + 1), DUR); }

            function show() {
                overlay.hidden = false;
                paint(); restart();
                document.body.style.overflow = 'hidden';
            }
            function hide() {
                clearInterval(timer); overlay.hidden = true;
                document.body.style.overflow = '';
                try { sessionStorage.setItem(KEY, '1'); } catch (e) {}
            }

            prev.addEventListener('click', () => go(idx - 1));
            next.addEventListener('click', () => go(idx + 1));
            closeBtn.addEventListener('click', hide);
            skipBtn.addEventListener('click', hide);
            overlay.addEventListener('click', (e) => { if (e.target === overlay) hide(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) clearInterval(timer); else restart();
            });

            setTimeout(show, 900);
        })();
    </script>
    @endguest

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
                            top: target.offsetTop - 80,
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