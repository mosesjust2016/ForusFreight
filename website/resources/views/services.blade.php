@extends('layouts.app')

@section('title', 'Our Services - Comprehensive Logistics Solutions')

@section('content')
    <!-- Hero Section -->
    <section style="position: relative; padding: 6rem 0; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%);">
        <div class="container" style="position: relative; z-index: 10;">
            <div style="max-width: 800px; margin: 0 auto; text-align: center; color: white;">
                <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1.5rem;">Our Comprehensive Services</h1>
                <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9);">
                    Tailored logistics solutions for businesses of all sizes
                </p>
            </div>
        </div>
    </section>

    <!-- Same-Day Delivery -->
    <section id="same-day" style="padding: 5rem 0; background: white;">
        <div class="container">
            <div class="service-section">
                <div class="service-content">
                    <span class="service-badge" style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); color: #007f7f;">
                        <i class="fas fa-clock" style="color: #ff6200;"></i> Express Service
                    </span>
                    <h2 class="service-title" style="color: #1e293b;">Same-Day Delivery</h2>
                    <p class="service-desc" style="color: #64748b;">
                        Need it delivered today? Our same-day delivery service ensures your urgent packages reach their destination within hours. 
                        Perfect for time-sensitive documents, medical supplies, and business-critical items.
                    </p>
                    
                    <div class="service-features">
                        <h3 style="color: #1e293b;">Who It's For:</h3>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Businesses with urgent documents</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> E-commerce companies</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Medical facilities</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Legal firms</li>
                        </ul>
                    </div>
                    
                    <div class="delivery-time-card" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 2px solid #007f7f;">
                        <i class="fas fa-bolt" style="color: #ff6200; font-size: 2.5rem;"></i>
                        <div>
                            <div style="font-size: 2rem; font-weight: 800; color: #1e293b;">2-8 Hours</div>
                            <div style="color: #64748b;">Within major city limits</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('quote') }}" class="service-cta" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <i class="fas fa-paper-plane"></i> Request Same-Day Quote
                    </a>
                </div>
                
                <div class="service-image">
                    <img src="https://images.unsplash.com/photo-1566576721346-d4a3b4eaeb55?w=800&q=80" alt="Same-Day Delivery">
                </div>
            </div>
        </div>
    </section>

    <!-- Cross-Border Shipping -->
    <section id="cross-border" style="padding: 5rem 0; background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="service-section" style="grid-template-columns: auto 1fr;">
                <div class="service-image">
                    <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80" alt="Cross-Border Shipping">
                </div>
                
                <div class="service-content">
                    <span class="service-badge" style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); color: #007f7f;">
                        <i class="fas fa-globe-africa" style="color: #ff6200;"></i> International
                    </span>
                    <h2 class="service-title" style="color: #1e293b;">Cross-Border Shipping</h2>
                    <p class="service-desc" style="color: #64748b;">
                        Expand your business across borders with our seamless cross-border shipping services. 
                        We handle customs clearance, documentation, and last-mile delivery across the SADC region.
                    </p>
                    
                    <div class="service-features">
                        <h3 style="color: #1e293b;">Who It's For:</h3>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Import/Export businesses</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Manufacturers</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Retail chains</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Mining companies</li>
                        </ul>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">3-5 Days</div>
                            <div style="color: #64748b;">Neighboring countries</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">5-10 Days</div>
                            <div style="color: #64748b;">Across SADC</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('quote') }}" class="service-cta" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <i class="fas fa-paper-plane"></i> Request Cross-Border Quote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Warehousing -->
    <section id="warehousing" style="padding: 5rem 0; background: white;">
        <div class="container">
            <div class="service-section">
                <div class="service-content">
                    <span class="service-badge" style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); color: #007f7f;">
                        <i class="fas fa-warehouse" style="color: #ff6200;"></i> Storage Solutions
                    </span>
                    <h2 class="service-title" style="color: #1e293b;">Warehousing & Storage</h2>
                    <p class="service-desc" style="color: #64748b;">
                        Our state-of-the-art warehouses provide secure, climate-controlled storage with advanced inventory management. 
                        Perfect for businesses needing storage solutions or distribution hubs.
                    </p>
                    
                    <div class="service-features">
                        <h3 style="color: #1e293b;">Who It's For:</h3>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> E-commerce businesses</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Wholesalers & distributors</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Manufacturing companies</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Seasonal inventory storage</li>
                        </ul>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #007f7f;">
                            <i class="fas fa-shield-alt" style="color: #ff6200; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <div style="font-weight: 600; color: #1e293b;">24/7 Security</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #007f7f;">
                            <i class="fas fa-thermometer-half" style="color: #ff6200; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <div style="font-weight: 600; color: #1e293b;">Climate Control</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #007f7f;">
                            <i class="fas fa-boxes" style="color: #ff6200; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <div style="font-weight: 600; color: #1e293b;">Inventory Mgmt</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #007f7f;">
                            <i class="fas fa-truck" style="color: #ff6200; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <div style="font-weight: 600; color: #1e293b;">Pick & Pack</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('quote') }}" class="service-cta" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <i class="fas fa-paper-plane"></i> Request Warehousing Quote
                    </a>
                </div>
                
                <div class="service-image">
                    <img src="https://images.unsplash.com/photo-1553413077-190dd305871c?w=800&q=80" alt="Warehousing">
                </div>
            </div>
        </div>
    </section>

    <!-- Bulk Cargo Transport -->
    <section id="bulk-cargo" style="padding: 5rem 0; background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="service-section" style="grid-template-columns: auto 1fr;">
                <div class="service-image">
                    <img src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=800&q=80" alt="Bulk Cargo">
                </div>
                
                <div class="service-content">
                    <span class="service-badge" style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); color: #007f7f;">
                        <i class="fas fa-truck-loading" style="color: #ff6200;"></i> Heavy Cargo
                    </span>
                    <h2 class="service-title" style="color: #1e293b;">Bulk Cargo Transport</h2>
                    <p class="service-desc" style="color: #64748b;">
                        Transport large volumes of goods efficiently with our specialized bulk cargo services. 
                        Our fleet includes flatbeds, trailers, and container trucks for industrial-scale transportation.
                    </p>
                    
                    <div class="service-features">
                        <h3 style="color: #1e293b;">Who It's For:</h3>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Mining companies</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Construction firms</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Agricultural businesses</li>
                            <li><i class="fas fa-check-circle" style="color: #059669;"></i> Industrial manufacturers</li>
                        </ul>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Flatbed Trucks</div>
                            <div style="color: #64748b;">Up to 30 tons</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Container Trucks</div>
                            <div style="color: #64748b;">20ft & 40ft</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Refrigerated</div>
                            <div style="color: #64748b;">Cold chain</div>
                        </div>
                        <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; padding: 1.5rem; border: 2px solid #007f7f;">
                            <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">Tankers</div>
                            <div style="color: #64748b;">Liquid transport</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('quote') }}" class="service-cta" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <i class="fas fa-paper-plane"></i> Request Bulk Cargo Quote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 5rem 0; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%);">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center; color: white;">
                <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem;">Ready to Streamline Your Logistics?</h2>
                <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9); margin-bottom: 2.5rem;">
                    Get a customized quote for your specific logistics needs. Our team will contact you within 24 hours.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('quote') }}" class="cta-button" style="background: #ff6200; color: white; padding: 1rem 2rem; border-radius: 9999px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                        <i class="fas fa-paper-plane"></i> Get Free Quote
                    </a>
                    <a href="tel:+1234567890" class="cta-button-alt" style="background: transparent; color: white; padding: 1rem 2rem; border-radius: 9999px; text-decoration: none; font-weight: 600; border: 2px solid white; transition: all 0.3s;">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .service-section {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4rem;
            align-items: center;
        }

        .service-content {
            max-width: 600px;
        }

        .service-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .service-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .service-desc {
            font-size: 1.125rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .service-features {
            margin-bottom: 2rem;
        }

        .service-features h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }

        .delivery-time-card {
            padding: 2rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .service-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            border-radius: 9999px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
        }

        .service-image {
            width: 500px;
        }

        .service-image img {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .cta-button:hover {
            background: #e55a00;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 98, 0, 0.3);
        }

        .cta-button-alt:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        @media (max-width: 1024px) {
            .service-section {
                grid-template-columns: 1fr !important;
            }

            .service-image {
                width: 100%;
                order: -1;
            }

            .service-content {
                max-width: 100%;
            }
        }
    </style>
@endsection