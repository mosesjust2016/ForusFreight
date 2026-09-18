<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - Forus Freight</title>
    <meta name="description" content="{{ Str::limit(strip_tags($page->content), 160) }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #1e293b; line-height: 1.6; }
        .lp-header { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); color: white; padding: 1.5rem 0; }
        .lp-header .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .lp-logo { font-size: 1.5rem; font-weight: 900; }
        .lp-logo i { color: #22c55e; margin-right: 0.5rem; }
        .lp-nav a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 600; opacity: 0.9; }
        .lp-nav a:hover { opacity: 1; }
        .lp-hero { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); color: white; padding: 4rem 0; text-align: center; }
        .lp-hero h1 { font-size: 2.5rem; font-weight: 900; margin-bottom: 1rem; }
        .lp-hero p { font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto 2rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        .lp-content { padding: 3rem 0; font-size: 1.1rem; }
        .lp-content h2 { font-size: 1.8rem; font-weight: 800; margin: 2rem 0 1rem; color: #1e293b; }
        .lp-content h3 { font-size: 1.3rem; font-weight: 800; margin: 1.5rem 0 0.75rem; }
        .lp-content p { margin-bottom: 1rem; color: #475569; }
        .lp-content ul, .lp-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
        .lp-content li { margin-bottom: 0.5rem; }
        .lp-cta { background: #f0fdf4; border-radius: 20px; padding: 3rem; text-align: center; margin: 3rem 0; }
        .lp-cta h2 { font-size: 1.8rem; font-weight: 900; margin-bottom: 1rem; color: #1e293b; }
        .lp-cta p { color: #475569; margin-bottom: 1.5rem; font-size: 1.1rem; }
        .lp-btn { display: inline-block; background: #22c55e; color: white; padding: 1rem 2rem; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 1.1rem; transition: all 0.2s; border: none; cursor: pointer; }
        .lp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(34,197,94,0.3); }
        .lp-form { max-width: 500px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .lp-form h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; text-align: center; }
        .lp-form-group { margin-bottom: 1.25rem; }
        .lp-form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem; }
        .lp-form-group input, .lp-form-group textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 1rem; outline: none; transition: all 0.2s; }
        .lp-form-group input:focus, .lp-form-group textarea:focus { border-color: #22c55e; }
        .lp-form-group textarea { min-height: 100px; resize: vertical; }
        .lp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin: 3rem 0; text-align: center; }
        .lp-stat { padding: 1.5rem; }
        .lp-stat-number { font-size: 2rem; font-weight: 900; color: #22c55e; }
        .lp-stat-label { font-size: 0.85rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 0.5rem; }
        .lp-footer { background: #1e293b; color: #94a3b8; padding: 2rem 0; text-align: center; font-size: 0.85rem; }
        .lp-footer a { color: #22c55e; text-decoration: none; }
        .success-message { background: #f0fdf4; color: #16a34a; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-weight: 700; text-align: center; }
    </style>
</head>
<body>
    <div class="lp-header">
        <div class="container">
            <div class="lp-logo"><i class="fas fa-truck"></i> Forus Freight</div>
            <div class="lp-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('quote') }}">Get Quote</a>
            </div>
        </div>
    </div>

    <div class="lp-hero">
        <div class="container">
            <h1>{{ $page->title }}</h1>
            <p>{{ Str::limit(strip_tags($page->content), 200) }}</p>
            <a href="#form" class="lp-btn"><i class="fas fa-paper-plane"></i> Get Started</a>
        </div>
    </div>

    <div class="container">
        <div class="lp-content">
            {!! $page->content !!}
        </div>

        <div class="lp-stats">
            <div class="lp-stat">
                <div class="lp-stat-number">500+</div>
                <div class="lp-stat-label">Shipments Monthly</div>
            </div>
            <div class="lp-stat">
                <div class="lp-stat-number">98%</div>
                <div class="lp-stat-label">On-Time Delivery</div>
            </div>
            <div class="lp-stat">
                <div class="lp-stat-number">24/7</div>
                <div class="lp-stat-label">Support Available</div>
            </div>
        </div>

        <div class="lp-cta" id="form">
            <h2>Ready to Ship?</h2>
            <p>Fill in your details and our team will contact you within 24 hours with a custom quote.</p>

            @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
            @endif

            <form class="lp-form" action="{{ route('public.lp.submit', $page->slug) }}" method="POST">
                @csrf
                <h3>Get Your Free Quote</h3>
                <div class="lp-form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required placeholder="John Banda">
                </div>
                <div class="lp-form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required placeholder="john@company.com">
                </div>
                <div class="lp-form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+260 97 123 4567">
                </div>
                <div class="lp-form-group">
                    <label>What are you shipping?</label>
                    <textarea name="message" placeholder="Tell us about your cargo, route, and timeline..."></textarea>
                </div>
                <button type="submit" class="lp-btn" style="width: 100%;"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </form>
        </div>
    </div>

    <div class="lp-footer">
        <div class="container">
            <p>This page was visited {{ $page->views }} times. {{ $page->submissions }} people submitted their details.</p>
            <p style="margin-top: 0.5rem;">&copy; {{ date('Y') }} Forus Freight. All rights reserved. <a href="{{ route('home') }}">Home</a> | <a href="{{ route('contact') }}">Contact</a></p>
        </div>
    </div>
</body>
</html>
