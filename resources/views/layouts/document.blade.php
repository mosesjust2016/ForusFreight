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
            color: #64748b;
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
<body>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @php
    $footerPage = \App\Models\CmsPage::where('slug', 'footer')->where('status', 'published')->first();
    $footer = $footerPage?->sections ?? [];
    @endphp

    <div role="region" aria-label="Quick contact actions">
        <!-- WhatsApp Float Button -->
        <a href="https://wa.me/{{ $footer['whatsapp_number'] ?? '260572788685' }}?text={{ urlencode($footer['whatsapp_message'] ?? 'Hi Forus Freight, I need a quote for logistics services') }}" target="_blank" class="whatsapp-float" aria-label="Chat with Forus Freight on WhatsApp">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
        </a>
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
                if (mobileMenu.classList.contains('active')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    mobileMenu.classList.remove('active');
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.className = 'fas fa-bars';
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
    </script>

    @yield('scripts')
</body>
</html>