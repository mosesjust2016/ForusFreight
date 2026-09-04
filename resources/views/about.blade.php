@php
$page = \App\Models\CmsPage::where('slug', 'about')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'About Us') . ' - Forus Freight')

@section('content')
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-hero-bg"></div>
        <div class="container" style="position: relative; z-index: 10; padding: 6rem 1rem;">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h1 style="font-size: 3.5rem; font-weight: 800; color: white; margin-bottom: 1.5rem;">{{ $sections['title'] ?? 'Our Story' }}</h1>
                <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9);">
                    {{ $sections['subtitle'] ?? 'Delivering excellence in logistics since 2010, connecting businesses across Zambia and the SADC region.' }}
                </p>
            </div>
        </div>
    </section>

    <!-- Company Story -->
    <section style="padding: 5rem 0; background: white;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr; gap: 3rem; align-items: center;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
                    <div>
                        {!! $sections['content'] ?? '<h2 style="font-size: 2.5rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem;">From Humble Beginnings</h2>
                        <p style="color: #64748b; margin-bottom: 1.5rem; font-size: 1.125rem; line-height: 1.7;">
                            Founded in 2010 with just two delivery vans, Forus Freight has grown into one of Zambia\'s leading logistics providers.
                        </p>' !!}
                        
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 2rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">14+</div>
                                <div style="color: #64748b; font-weight: 500;">Years Experience</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">500+</div>
                                <div style="color: #64748b; font-weight: 500;">Happy Clients</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">150+</div>
                                <div style="color: #64748b; font-weight: 500;">Fleet Vehicles</div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <img src="https://images.unsplash.com/photo-1566576721346-d4a3b4eaeb55?w=800&q=80" 
                             alt="Our Logistics Team" 
                             style="width: 100%; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section style="padding: 5rem 0; background: linear-gradient(180deg, #f0f9f9 0%, #e0f2f2 100%);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                <!-- Mission -->
                <div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #007f7f;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 2px solid #007f7f;">
                        <i class="fas fa-bullseye" style="color: #ff6200; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem;">Our Mission</h3>
                    <p style="color: #64748b; margin-bottom: 1.5rem; line-height: 1.7;">
                        {{ $sections['mission'] ?? 'To provide fast, reliable, and affordable logistics solutions that empower businesses to grow and thrive.' }}
                    </p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-check-circle" style="color: #059669; margin-right: 0.75rem;"></i> 
                            Deliver excellence in every shipment
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-check-circle" style="color: #059669; margin-right: 0.75rem;"></i> 
                            Innovate for better efficiency
                        </li>
                        <li style="display: flex; align-items: center; color: #1e293b;">
                            <i class="fas fa-check-circle" style="color: #059669; margin-right: 0.75rem;"></i> 
                            Build lasting partnerships
                        </li>
                    </ul>
                </div>
                
                <!-- Vision -->
                <div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #007f7f;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 2px solid #007f7f;">
                        <i class="fas fa-eye" style="color: #ff6200; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem;">Our Vision</h3>
                    <p style="color: #64748b; margin-bottom: 1.5rem; line-height: 1.7;">
                        To become the leading logistics provider in Southern Africa, known for reliability, innovation, 
                        and exceptional customer service. We envision a connected region where businesses can operate seamlessly.
                    </p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-star" style="color: #ff6200; margin-right: 0.75rem;"></i> 
                            Regional logistics leader by 2026
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-star" style="color: #ff6200; margin-right: 0.75rem;"></i> 
                            Digital-first logistics solutions
                        </li>
                        <li style="display: flex; align-items: center; color: #1e293b;">
                            <i class="fas fa-star" style="color: #ff6200; margin-right: 0.75rem;"></i> 
                            Sustainable operations
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Operating Regions -->
    <section style="padding: 5rem 0; background: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.5rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem;">Where We Operate</h2>
                <p style="color: #64748b; font-size: 1.125rem;">Extensive coverage across Zambia and the SADC region</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
                <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 24px; padding: 2.5rem; border: 2px solid #007f7f;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">Major Zambian Cities</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-map-marker-alt" style="color: #007f7f; margin-right: 0.75rem;"></i> Lusaka (HQ)
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-map-marker-alt" style="color: #007f7f; margin-right: 0.75rem;"></i> Ndola & Kitwe
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-map-marker-alt" style="color: #007f7f; margin-right: 0.75rem;"></i> Livingstone
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-map-marker-alt" style="color: #007f7f; margin-right: 0.75rem;"></i> Chipata
                        </li>
                        <li style="display: flex; align-items: center; color: #1e293b;">
                            <i class="fas fa-map-marker-alt" style="color: #007f7f; margin-right: 0.75rem;"></i> Solwezi
                        </li>
                    </ul>
                </div>
                
                <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 24px; padding: 2.5rem; border: 2px solid #059669;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">SADC Countries</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-flag" style="color: #059669; margin-right: 0.75rem;"></i> South Africa
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-flag" style="color: #059669; margin-right: 0.75rem;"></i> Botswana
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-flag" style="color: #059669; margin-right: 0.75rem;"></i> Namibia
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-flag" style="color: #059669; margin-right: 0.75rem;"></i> Tanzania
                        </li>
                        <li style="display: flex; align-items: center; color: #1e293b;">
                            <i class="fas fa-flag" style="color: #059669; margin-right: 0.75rem;"></i> Zimbabwe
                        </li>
                    </ul>
                </div>
                
                <div style="background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius: 24px; padding: 2.5rem; border: 2px solid #ff6200;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">Expanding Soon</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-rocket" style="color: #ff6200; margin-right: 0.75rem;"></i> Mozambique
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-rocket" style="color: #ff6200; margin-right: 0.75rem;"></i> Malawi
                        </li>
                        <li style="display: flex; align-items: center; margin-bottom: 0.75rem; color: #1e293b;">
                            <i class="fas fa-rocket" style="color: #ff6200; margin-right: 0.75rem;"></i> DRC
                        </li>
                        <li style="display: flex; align-items: center; color: #1e293b;">
                            <i class="fas fa-rocket" style="color: #ff6200; margin-right: 0.75rem;"></i> Angola
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Safety & Reliability -->
    <section style="padding: 5rem 0; background: linear-gradient(135deg, #007f7f 0%, #005f5f 50%, #059669 100%); color: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">Our Safety & Reliability Promise</h2>
                <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9); max-width: 800px; margin: 0 auto;">
                    We prioritize the safety of your goods and guarantee reliable delivery
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 24px; padding: 2.5rem; text-align: center; border: 2px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #007f7f;">
                        <i class="fas fa-shield-alt" style="color: #007f7f; font-size: 2rem;"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Secure Handling</h3>
                    <p style="color: rgba(255,255,255,0.9);">All shipments are insured and handled with care by trained professionals</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 24px; padding: 2.5rem; text-align: center; border: 2px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #007f7f;">
                        <i class="fas fa-clock" style="color: #007f7f; font-size: 2rem;"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">On-Time Delivery</h3>
                    <p style="color: rgba(255,255,255,0.9);">98% on-time delivery rate with real-time tracking updates</p>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 24px; padding: 2.5rem; text-align: center; border: 2px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #007f7f;">
                        <i class="fas fa-headset" style="color: #007f7f; font-size: 2rem;"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">24/7 Support</h3>
                    <p style="color: rgba(255,255,255,0.9);">Round-the-clock customer support for any queries or concerns</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        .about-hero {
            position: relative;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .about-hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #007f7f 0%, #005f5f 50%, #059669 100%);
        }

        @media (max-width: 768px) {
            section > .container > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection