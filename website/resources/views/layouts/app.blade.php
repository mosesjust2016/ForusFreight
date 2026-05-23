<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Forus Freight - Global logistics solutions across Zambia and SADC region. Fast, reliable, and professional freight services.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Forus Freight - Global Logistics Solutions')</title>
    
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
                <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
                <li><a href="{{ route('quote') }}" class="nav-btn-outline">Get Quote</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="nav-btn">Dashboard</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="nav-btn">Login</a></li>
                @endauth
            </ul>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">Services</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ route('tracking') }}" class="{{ request()->routeIs('tracking') ? 'active' : '' }}">Track</a></li>
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
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" style="height: 56px; width: auto; display: block;">
                    </div>
                    <p style="color: #94a3b8; margin-bottom: 1.5rem;">Fast, reliable & affordable logistics solutions across Zambia and the SADC region.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="footer-title">Our Services</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('services') }}#same-day"><i class="fas fa-arrow-right"></i> Same-Day Delivery</a></li>
                        <li><a href="{{ route('services') }}#cross-border"><i class="fas fa-arrow-right"></i> Cross-Border Shipping</a></li>
                        <li><a href="{{ route('services') }}#warehousing"><i class="fas fa-arrow-right"></i> Warehousing</a></li>
                        <li><a href="{{ route('services') }}#bulk-cargo"><i class="fas fa-arrow-right"></i> Bulk Cargo</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="footer-title">Company</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('about') }}"><i class="fas fa-arrow-right"></i> About Us</a></li>
                        <li><a href="{{ route('services') }}"><i class="fas fa-arrow-right"></i> Services</a></li>
                        <li><a href="{{ route('quote') }}"><i class="fas fa-arrow-right"></i> Get Quote</a></li>
                        <li><a href="{{ route('tracking') }}"><i class="fas fa-arrow-right"></i> Track Shipment</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="footer-title">Get in Touch</h3>
                    <div class="footer-contact">
                        <i class="fas fa-phone"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">+260 572 7886857</span>
                            <span style="display: block; font-weight: 600;">+260 766 193059</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">24/7 Support</span>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">info@forusfl.co.zm</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">Email Us</span>
                        </div>
                    </div>
                    <div class="footer-contact">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Forus Freight Ltd METROLUX PLAZA Plot No. 401A/8 Kafue Road</span>
                            <span style="font-size: 0.875rem; color: #cbd5e1;">Lusaka, Zambia</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Forus Freight. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/terms">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/260961234567?text=Hi%20Forus%20Freight,%20I%20need%20a%20quote%20for%20logistics%20services" target="_blank" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

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

        // Form validation for quote page
        document.addEventListener('DOMContentLoaded', function() {
            const quoteForm = document.getElementById('quoteForm');
            if (quoteForm) {
                quoteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    submitBtn.disabled = true;
                    
                    // Simulate API call (replace with actual AJAX call)
                    setTimeout(() => {
                        // Show success message
                        const successDiv = document.getElementById('formSuccess');
                        if (successDiv) {
                            successDiv.style.display = 'flex';
                            successDiv.classList.add('animate-fade-in');
                        }
                        
                        // Reset form
                        this.reset();
                        
                        // Reset button
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        
                        // Hide success message after 5 seconds
                        setTimeout(() => {
                            if (successDiv) {
                                successDiv.style.display = 'none';
                            }
                        }, 5000);
                    }, 2000);
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>