@extends('layouts.app')

@php
try {
    $page = \App\Models\CmsPage::where('slug', 'home')->first();
    $sections = $page?->sections ?? [];
} catch (\Exception $e) {
    $page = null;
    $sections = [];
}
@endphp
@section('title', ($page?->title ?? 'Forus Freight') . ' - Global Logistics Solutions')

@section('styles')
    <!-- Globe.gl Library -->
    <script src="//unpkg.com/globe.gl"></script>
    
    <style>
        /* COLOR VARIABLES - YOUR BRAND COLORS */
        :root {
            --primary: rgb(0, 127, 127);    /* Main brand color */
            --secondary: rgb(255, 98, 0);   /* Secondary brand color */
            --tertiary: rgb(204, 204, 204); /* Tertiary color */
            --dark: #0f172a;
            --light: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        /* Hero Section - USING PRIMARY COLOR */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary) 0%, rgba(0, 150, 150, 0.9) 100%);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.1;
            background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 900px;
            padding: 2rem;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            min-height: 1.2em;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-weight: 300;
            min-height: 1.5em;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-secondary {
            background: transparent;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid white;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary);
        }

        .btn-tertiary {
            background: transparent;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-tertiary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

        /* Generic Button Styles */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
            border: 2px solid var(--secondary);
        }

        .btn-primary:hover {
            background: white;
            color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 50px;
            border: 2px solid white;
            border-radius: 25px;
            display: flex;
            justify-content: center;
            padding-top: 0.5rem;
        }

        .scroll-dot {
            width: 6px;
            height: 12px;
            background: white;
            border-radius: 3px;
            animation: scroll 2s infinite;
        }

        @keyframes scroll {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(10px);
            }
        }

        /* Service Icons Strip */
        .service-strip {
            background: white;
            padding: 3rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .service-strip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .service-icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 12px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .service-icon-item:hover {
            background: #f1f5f9;
            transform: translateY(-5px);
        }

        .service-icon-item i {
            font-size: 2.5rem;
            color: var(--secondary);
            transition: transform 0.3s;
        }

        .service-icon-item:hover i {
            transform: scale(1.1);
        }

        .service-icon-item span {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Industries Section */
        .industries-section {
            padding: 5rem 0;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.125rem;
            color: var(--text-light);
        }

        .industries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .industry-card {
            position: relative;
            height: 200px;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .industry-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Industry card backgrounds using brand colors */
        .industry-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--primary);
            transition: opacity 0.3s;
        }

        .industry-card:nth-child(2)::before { background: var(--secondary); }
        .industry-card:nth-child(3)::before { background: var(--tertiary); }
        .industry-card:nth-child(4)::before { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); }
        .industry-card:nth-child(5)::before { background: linear-gradient(135deg, var(--secondary) 0%, #ff6b00 100%); }
        .industry-card:nth-child(6)::before { background: linear-gradient(135deg, var(--primary) 0%, #009999 100%); }
        .industry-card:nth-child(7)::before { background: linear-gradient(135deg, var(--tertiary) 0%, #999 100%); }
        .industry-card:nth-child(8)::before { background: linear-gradient(135deg, var(--secondary) 0%, #ff4500 100%); }

        .industry-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            transition: background 0.3s;
        }

        .industry-card:hover::after {
            background: rgba(0, 0, 0, 0.5);
        }

        .industry-card-content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 1.5rem;
            text-align: center;
        }

        /* Stats Section - USING PRIMARY COLOR */
        .stats-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary) 0%, rgba(0, 150, 150, 0.9) 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item {
            animation: fadeInUp 1s ease-out;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Global Network */
        .network-section {
            padding: 5rem 0;
            background: white;
        }

        .network-map {
            background: rgba(0, 127, 127, 0.05);
            border-radius: 24px;
            padding: 4rem;
            text-align: center;
            margin-top: 3rem;
            border: 2px solid rgba(0, 127, 127, 0.1);
        }

        /* 3D Globe Container */
        .globe-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3rem;
            padding: 2rem;
        }

        .globe-wrapper {
            position: relative;
            width: 600px;
            height: 600px;
            margin: 0 auto;
        }

        #globeViz {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 
                0 0 60px rgba(0, 127, 127, 0.3),
                inset 0 0 40px rgba(0, 127, 127, 0.2);
        }
        
        #globeViz canvas {
            display: block;
        }

        .globe-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
            z-index: 100;
        }

        .globe-control-btn {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .globe-control-btn:hover {
            background: var(--secondary);
            transform: scale(1.1);
        }

        /* Country markers on globe */
        .globe-marker {
            position: absolute;
            width: 16px;
            height: 16px;
            background: var(--secondary);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            z-index: 10;
            animation: pulse 2s infinite;
            box-shadow: 0 0 15px rgba(255, 98, 0, 0.7);
        }

        .globe-marker::after {
            content: attr(data-country);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .globe-marker:hover::after {
            opacity: 1;
        }

        .globe-marker:hover {
            animation: none;
            transform: translate(-50%, -50%) scale(1.5);
            background: var(--primary);
        }

        @keyframes pulse {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1);
                box-shadow: 0 0 15px rgba(255, 98, 0, 0.7);
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.2);
                box-shadow: 0 0 25px rgba(255, 98, 0, 0.9);
            }
        }

        /* Connection lines */
        .globe-connection {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
        }

        .connection-line {
            position: absolute;
            background: var(--secondary);
            height: 2px;
            transform-origin: 0 0;
            opacity: 0.3;
            transition: opacity 0.3s;
        }

        .connection-line.active {
            opacity: 0.7;
            background: var(--primary);
        }

        /* Country Info Panel */
        .country-info {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            border: 2px solid rgba(0, 127, 127, 0.1);
        }

        .country-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .country-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(0, 127, 127, 0.05);
        }

        .country-item:hover {
            background: rgba(0, 127, 127, 0.1);
            transform: translateY(-2px);
        }

        .country-item.active {
            background: rgba(0, 127, 127, 0.15);
            border: 2px solid #007f7f;
        }

        .country-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .country-item h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .country-item p {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Network Stats below globe */
        .network-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
            text-align: center;
        }

        .network-stat {
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .network-stat:hover {
            transform: translateY(-5px);
        }

        .network-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #007f7f;
            margin-bottom: 0.5rem;
        }

        .network-stat-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        /* News Section */
        .news-section {
            padding: 5rem 0;
            background: #f8fafc;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .news-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid rgba(0, 127, 127, 0.1);
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 127, 127, 0.15);
            border-color: var(--primary);
        }

        .news-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--tertiary) 0%, #aaa 100%);
        }

        .news-content {
            padding: 1.5rem;
        }

        .news-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .news-excerpt {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .news-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .news-link:hover {
            color: var(--secondary);
        }

        /* CTA Section - USING PRIMARY COLOR */
        .cta-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--dark) 0%, #1a202c 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .cta-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: var(--secondary);
            color: white;
            padding: 1rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.125rem;
            display: inline-block;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid var(--secondary);
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 98, 0, 0.4);
            background: white;
            color: var(--secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.125rem;
            }

            .cta-title {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .industry-card-content {
                font-size: 1.125rem;
            }
            
            .globe-wrapper {
                width: 400px;
                height: 400px;
            }
            
            .country-list {
                grid-template-columns: 1fr;
            }
            
            .network-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .cta-title {
                font-size: 1.75rem;
            }
            
            .section-title h2 {
                font-size: 1.75rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }
            
            .globe-wrapper {
                width: 300px;
                height: 300px;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                text-align: center;
            }
            
            .network-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-pattern"></div>
        
        <div class="hero-content">
            <h1 class="hero-title">{{ $sections['hero_title'] ?? 'Global Logistics Solutions' }}</h1>
            <p class="hero-subtitle">{{ $sections['hero_subtitle'] ?? 'Worldwide freight solutions across Zambia & the SADC region' }}</p>
            <div class="hero-buttons">
                <a href="{{ $sections['hero_cta_link'] ?? route('quote') }}" class="btn btn-primary">{{ $sections['hero_cta_text'] ?? 'Get a Free Quote' }}</a>
                <a href="{{ route('about') }}" class="btn btn-outline">Learn More</a>
            </div>
        </div>

        <div class="scroll-indicator">
            <div class="scroll-dot"></div>
        </div>
    </section>

    <!-- Service Icons Strip -->
    <section class="service-strip">
        <div class="container">
            <div class="service-strip-grid">
                <div class="service-icon-item">
                    <i class="fas fa-file-invoice"></i>
                    <span>Customs</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-truck"></i>
                    <span>Road Freight</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-plane"></i>
                    <span>Air Freight</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-ship"></i>
                    <span>Sea Freight</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-bolt"></i>
                    <span>Express</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-warehouse"></i>
                    <span>Warehousing</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-industry"></i>
                    <span>Project Cargo</span>
                </div>
                <div class="service-icon-item">
                    <i class="fas fa-cogs"></i>
                    <span>Special Logistics</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Industries Section -->
    <section class="industries-section">
        <div class="container">
            <div class="section-title">
                <h2>Logistics Solutions Tailored to Your Industry</h2>
                <p>Specialized solutions designed for your specific sector needs</p>
            </div>

            <div class="industries-grid">
                <div class="industry-card">
                    <div class="industry-card-content">Healthcare & Pharmaceutical</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Automotive & Aviation</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Fashion & Retail</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Electronics & Technology</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Food & Beverage</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Construction & Industrial</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Mining & Resources</div>
                </div>
                <div class="industry-card">
                    <div class="industry-card-content">Oil & Gas</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white;">Forus Freight in Numbers</h2>
                <p style="color: rgba(255,255,255,0.8);">Growing with our clients for over a decade</p>
            </div>

            <div class="stats-grid">
                @foreach($sections['stats'] ?? [['number'=>'150+','label'=>'Fleet Vehicles'],['number'=>'20+','label'=>'Warehouses'],['number'=>'75K+','label'=>'Shipments Annually'],['number'=>'12','label'=>'SADC Countries']] as $stat)
                <div class="stat-item">
                    <div class="stat-number">{{ $stat['number'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
                @endforeach
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support Available</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Happy Clients</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Network -->
    <section class="network-section">
        <div class="container">
            <div class="section-title">
                <h2>Global Network Coverage</h2>
                <p>Moving goods across Africa and beyond with direct presence</p>
            </div>

            <div class="network-map">
                <div class="globe-container">
                    <!-- 3D Globe Container -->
                    <div class="globe-wrapper">
                        <div id="globeViz" style="width:100%;height:100%;"></div>
                        
                        <!-- Globe Controls -->
                        <div class="globe-controls">
                            <button class="globe-control-btn" id="zoomIn">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="globe-control-btn" id="zoomOut">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="globe-control-btn" id="resetView">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="globe-control-btn" id="rotateToggle">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Country Info Panel -->
                    <div class="country-info">
                        <div class="country-list">
                            <div class="country-item active" data-country="Zambia">
                                <div class="country-marker" style="background: #007f7f;"></div>
                                <div>
                                    <h4>Zambia</h4>
                                    <p>Headquarters & Main Operations</p>
                                </div>
                            </div>
                            <div class="country-item" data-country="South Africa">
                                <div class="country-marker" style="background: #ff6200;"></div>
                                <div>
                                    <h4>South Africa</h4>
                                    <p>Major Hub for Southern Africa</p>
                                </div>
                            </div>
                            <div class="country-item" data-country="Tanzania">
                                <div class="country-marker" style="background: #059669;"></div>
                                <div>
                                    <h4>Tanzania</h4>
                                    <p>East Africa Gateway</p>
                                </div>
                            </div>
                            <div class="country-item" data-country="Botswana">
                                <div class="country-marker" style="background: #8b5cf6;"></div>
                                <div>
                                    <h4>Botswana</h4>
                                    <p>Regional Distribution Center</p>
                                </div>
                            </div>
                            <div class="country-item" data-country="Mozambique">
                                <div class="country-marker" style="background: #f59e0b;"></div>
                                <div>
                                    <h4>Mozambique</h4>
                                    <p>Port Operations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Network Stats -->
                    <div class="network-stats">
                        <div class="network-stat">
                            <div class="network-stat-number">12+</div>
                            <div class="network-stat-label">Countries Served</div>
                        </div>
                        <div class="network-stat">
                            <div class="network-stat-number">50+</div>
                            <div class="network-stat-label">Warehouses</div>
                        </div>
                        <div class="network-stat">
                            <div class="network-stat-number">24/7</div>
                            <div class="network-stat-label">Operations</div>
                        </div>
                        <div class="network-stat">
                            <div class="network-stat-number">99%</div>
                            <div class="network-stat-label">On-time Delivery</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <div class="section-title">
                <h2>Latest News & Updates</h2>
                <p>Stay informed about our latest developments</p>
            </div>

            <div class="news-grid">
                <div class="news-card">
                    <div class="news-image"></div>
                    <div class="news-content">
                        <h3 class="news-title">Fleet Expansion Announcement</h3>
                        <p class="news-excerpt">Forus Freight expands operations with 50 new trucks to serve the SADC region better.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image"></div>
                    <div class="news-content">
                        <h3 class="news-title">New Warehouse Opens in Lusaka</h3>
                        <p class="news-excerpt">State-of-the-art 15,000 sqm facility now operational for enhanced storage solutions.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image"></div>
                    <div class="news-content">
                        <h3 class="news-title">Award-Winning Service Excellence</h3>
                        <p class="news-excerpt">Recognized as Best Logistics Provider 2024 for outstanding customer service.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Ship with Us?</h2>
            <p class="cta-subtitle">Get started today with a free, no-obligation quote</p>
            <a href="{{ route('quote') }}" class="btn-cta">Request Free Quote</a>
            @guest
                <a href="{{ route('login') }}" class="btn-cta" style="background: white; color: var(--secondary);">Login to Account</a>
            @endguest
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Globe.gl Implementation
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('globeViz');

            const countries = {
                'Zambia':       { lat: -13.1339, lng: 27.8493, color: '#007f7f', size: 0.7 },
                'South Africa': { lat: -30.5595, lng: 22.9375, color: '#ff6200', size: 0.6 },
                'Zimbabwe':     { lat: -19.0154, lng: 29.1549, color: '#007f7f', size: 0.6 },
                'China':        { lat:  35.8617, lng: 104.1954, color: '#ff6200', size: 0.7 },
                'UAE':          { lat:  23.4241, lng: 53.8478, color: '#059669', size: 0.6 },
                'Tanzania':     { lat:  -6.3690, lng: 34.8888, color: '#059669', size: 0.5 },
                'Botswana':     { lat: -22.3285, lng: 24.6849, color: '#8b5cf6', size: 0.5 },
                'Mozambique':   { lat: -18.6657, lng: 35.5296, color: '#f59e0b', size: 0.5 },
                'DRC':          { lat:  -4.0383, lng: 21.7587, color: '#ff6200', size: 0.4 },
                'Angola':       { lat: -11.2027, lng: 17.8739, color: '#007f7f', size: 0.4 },
                'Kenya':        { lat:   1.2921, lng: 36.8219, color: '#059669', size: 0.4 },
                'Ethiopia':     { lat:   9.1450, lng: 40.4897, color: '#f59e0b', size: 0.4 },
            };

            const connections = [
                ['Zambia','South Africa'],['Zambia','Tanzania'],['Zambia','Botswana'],
                ['Zambia','Mozambique'],['Zambia','Zimbabwe'],['Zambia','DRC'],
                ['Zambia','Angola'],['Zambia','UAE'],['Zambia','China'],
                ['South Africa','Botswana'],['South Africa','China'],
                ['Tanzania','Kenya'],['Kenya','Ethiopia'],['UAE','China'],
            ];

            const points = Object.entries(countries).map(([name, d]) => ({
                name, lat: d.lat, lng: d.lng, color: d.color, size: d.size,
            }));

            const arcs = connections.map(([from, to]) => ({
                startLat: countries[from].lat, startLng: countries[from].lng,
                endLat:   countries[to].lat,   endLng:   countries[to].lng,
                color: ['rgba(0,127,127,0.6)', 'rgba(0,127,127,0.6)'],
            }));

            const myGlobe = Globe()
                .width(container.clientWidth)
                .height(container.clientHeight)
                .backgroundColor('rgba(0,0,0,0)')
                .globeImageUrl('//unpkg.com/three-globe/example/img/earth-blue-marble.jpg')
                .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
                .pointsData(points)
                .pointColor(d => d.color)
                .pointAltitude(0.02)
                .pointRadius(d => d.size)
                .pointLabel(d => `<span style="padding:4px 8px;background:rgba(0,0,0,0.8);color:#fff;border-radius:6px;font-size:12px;font-weight:700;">${d.name}</span>`)
                .arcsData(arcs)
                .arcColor('color')
                .arcDashLength(0.5)
                .arcDashGap(0.15)
                .arcDashAnimateTime(2200)
                .arcStroke(0.6)
                (container);

            myGlobe.pointOfView({ lat: -15, lng: 28, altitude: 1.8 });
            myGlobe.controls().autoRotate = true;
            myGlobe.controls().autoRotateSpeed = 0.4;

            let rotating = true;

            document.getElementById('zoomIn').addEventListener('click', () => {
                const pov = myGlobe.pointOfView();
                myGlobe.pointOfView({ ...pov, altitude: Math.max(0.5, pov.altitude * 0.8) }, 400);
            });
            document.getElementById('zoomOut').addEventListener('click', () => {
                const pov = myGlobe.pointOfView();
                myGlobe.pointOfView({ ...pov, altitude: Math.min(5, pov.altitude * 1.25) }, 400);
            });
            document.getElementById('resetView').addEventListener('click', () => {
                myGlobe.pointOfView({ lat: -15, lng: 28, altitude: 1.8 }, 700);
            });
            document.getElementById('rotateToggle').addEventListener('click', (e) => {
                rotating = !rotating;
                myGlobe.controls().autoRotate = rotating;
                e.target.closest('button').querySelector('i').className =
                    rotating ? 'fas fa-pause' : 'fas fa-play';
            });

            document.querySelectorAll('.country-item').forEach(item => {
                item.addEventListener('click', () => {
                    const name = item.getAttribute('data-country');
                    const d = countries[name];
                    if (d) myGlobe.pointOfView({ lat: d.lat, lng: d.lng, altitude: 1.2 }, 800);
                    document.querySelectorAll('.country-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });

            window.addEventListener('resize', () => {
                myGlobe.width(container.clientWidth).height(container.clientHeight);
            });

            // Other animations and interactions
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all sections
            document.querySelectorAll('section').forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(section);
            });
            
            // Add animation to stats on scroll
            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                const statsObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const stats = entry.target.querySelectorAll('.stat-number');
                            stats.forEach(stat => {
                                const text = stat.textContent;
                                const target = parseInt(text.replace('+', ''));
                                let current = 0;
                                const increment = target / 50;
                                const timer = setInterval(() => {
                                    current += increment;
                                    if (current >= target) {
                                        stat.textContent = text;
                                        clearInterval(timer);
                                    } else {
                                        stat.textContent = Math.floor(current) + (text.includes('+') ? '+' : '');
                                    }
                                }, 30);
                            });
                            statsObserver.unobserve(entry.target);
                        }
                    });
                });
                statsObserver.observe(statsSection);
            }
        });
    </script>
@endsection