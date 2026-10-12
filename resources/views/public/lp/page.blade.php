<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page->hero_title ?: $page->title }} - Forus Freight</title>
    <meta name="description" content="{{ Str::limit(strip_tags($page->hero_text ?: $page->content), 160) }}">
    <meta name="robots" content="index,follow">
    <meta name="theme-color" content="#007f7f">
    <meta property="og:site_name" content="Forus Freight">
    <link rel="canonical" href="{{ url('/lp/'.$page->slug) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $page->hero_title ?: $page->title }} - Forus Freight">
    <meta property="og:description" content="{{ Str::limit(strip_tags($page->hero_text ?: $page->content), 160) }}">
    <meta property="og:url" content="{{ url('/lp/'.$page->slug) }}">
    <meta property="og:image" content="{{ $page->hero_image_url ? asset('storage/'.ltrim($page->hero_image_url,'/')) : asset('images/logo-transparent.png') }}">
    <meta property="og:image:alt" content="{{ $page->title }} - Forus Freight">
    <meta property="og:locale" content="en_ZM">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->hero_title ?: $page->title }} - Forus Freight">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($page->hero_text ?: $page->content), 160) }}">
    <meta name="twitter:image" content="{{ $page->hero_image_url ? asset('storage/'.ltrim($page->hero_image_url,'/')) : asset('images/logo-transparent.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-180.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #0d1b20; line-height: 1.6; background:#f5f4ef; }
        :root { --lp-primary: {{ $page->primary_color ?: '#ff6200' }}; --lp-accent: {{ $page->accent_color ?: '#007f7f' }}; }
        .lp-header { background: #fff; color: #0d1b20; padding: 1.15rem 0; border-bottom:1px solid rgba(13,27,32,.08); }
        .lp-header .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .lp-logo { display:flex; align-items:center; gap:.65rem; font-size:1.5rem; font-weight:900; }
        .lp-logo img { width:150px; height:auto; max-height:48px; object-fit:contain; }
        .lp-logo i { color: var(--lp-primary); margin-right: 0.5rem; }
        .lp-nav a { color: #007f7f; text-decoration: none; margin-left: 1.5rem; font-weight: 800; opacity: 0.9; }
        .lp-nav a:hover { opacity: 1; }
        .lp-hero { background: linear-gradient(135deg, var(--lp-accent) 0%, #00a6a6 100%); color: white; padding: 6rem 0; text-align: center; background-size:cover; background-position:center; position:relative; overflow:hidden; }
        .lp-hero .container { max-width:980px; }
        .lp-kicker { display:inline-flex; padding:.4rem .7rem; border:1px solid rgba(255,255,255,.35); border-radius:999px; color:#fff; font-size:.72rem; font-weight:900; letter-spacing:.1em; text-transform:uppercase; margin-bottom:1rem; }
        .lp-hero h1 { font-size: clamp(2.5rem,6vw,4.8rem); line-height:1.02; letter-spacing:-.06em; font-weight: 900; margin-bottom: 1.25rem; }
        .lp-hero p { font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto 2rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        .lp-content { padding: 4rem 0; font-size: 1.1rem; }
        .lp-content h2 { font-size: 1.8rem; font-weight: 800; margin: 2rem 0 1rem; color: #1e293b; }
        .lp-content h3 { font-size: 1.3rem; font-weight: 800; margin: 1.5rem 0 0.75rem; }
        .lp-content p { margin-bottom: 1rem; color: #475569; }
        .lp-content ul, .lp-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
        .lp-content li { margin-bottom: 0.5rem; }
        .lp-gallery { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin:2rem 0 3rem; }
        .lp-gallery figure { margin:0; min-height:180px; overflow:hidden; border-radius:18px; background:#fff; box-shadow:0 12px 28px rgba(13,27,32,.1); }
        .lp-gallery img { width:100%; height:220px; display:block; object-fit:cover; transition:transform .35s ease; }
        .lp-gallery figure:hover img { transform:scale(1.04); }
        .lp-gallery figcaption { padding:.85rem 1rem; color:#56666c; font-size:.78rem; font-weight:800; }
        .lp-cta { background: #fff; border:1px solid rgba(13,27,32,.1); border-radius:24px; padding:3rem; text-align:center; margin:3rem 0 5rem; box-shadow:0 18px 50px rgba(13,27,32,.09); }
        .lp-cta h2 { font-size: 1.8rem; font-weight: 900; margin-bottom: 1rem; color: #1e293b; }
        .lp-cta p { color: #475569; margin-bottom: 1.5rem; font-size: 1.1rem; }
        .lp-btn { display: inline-block; background: var(--lp-primary); color: #0d1b20; padding: 1rem 2rem; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 1.1rem; transition: all 0.2s; border: none; cursor: pointer; }
        .lp-btn-secondary { display:inline-block; padding:1rem 1.4rem; border:1px solid rgba(255,255,255,.7); color:#fff; border-radius:12px; font-weight:800; text-decoration:none; font-size:1rem; margin-left:.55rem; }
        .lp-btn-secondary:hover { background:rgba(255,255,255,.14); }
        .lp-hero-actions { display:flex; justify-content:center; align-items:center; flex-wrap:wrap; gap:.4rem; }
        .lp-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(34,197,94,0.3); }
        .lp-benefits { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin:-2.2rem auto 1rem; position:relative; z-index:2; }
        .lp-benefit { background:#fff; border:1px solid rgba(13,27,32,.08); border-radius:16px; padding:1.2rem; box-shadow:0 12px 28px rgba(13,27,32,.08); }
        .lp-benefit i { color:var(--lp-primary); font-size:1.1rem; margin-bottom:.65rem; }
        .lp-benefit strong { display:block; color:#0d1b20; font-size:.92rem; }
        .lp-benefit span { display:block; color:#56666c; font-size:.8rem; margin-top:.25rem; }
        .lp-trust { display:flex; justify-content:center; gap:1.5rem; flex-wrap:wrap; padding:1rem 0 0; color:#56666c; font-size:.78rem; font-weight:700; }
        .lp-trust span { display:inline-flex; align-items:center; gap:.4rem; }
        .lp-trust i { color:var(--lp-accent); }
        .lp-share { display:flex; justify-content:center; align-items:center; gap:.5rem; flex-wrap:wrap; padding:1.5rem 0 .25rem; }
        .lp-share span { color:#56666c; font-size:.75rem; font-weight:800; }
        .lp-share a { display:inline-flex; align-items:center; gap:.3rem; padding:.42rem .62rem; border-radius:8px; color:#fff; font-size:.72rem; font-weight:800; text-decoration:none; }
        .lp-share .share-wa { background:#16a34a; } .lp-share .share-li { background:#0a66c2; } .lp-share .share-fb { background:#1877f2; } .lp-share .share-x { background:#111827; }
        .lp-form { max-width: 560px; margin: 2rem auto 0; background: #f5f4ef; padding: 2rem; border-radius: 18px; border:1px solid rgba(13,27,32,.08); box-shadow:none; text-align:left; }
        .lp-form h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; text-align: center; }
        .lp-form-group { margin-bottom: 1.25rem; }
        .lp-form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem; }
        .lp-form-group input, .lp-form-group textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 1rem; outline: none; transition: all 0.2s; }
        .lp-form-group input:focus, .lp-form-group textarea:focus { border-color: #22c55e; }
        .lp-form-group textarea { min-height: 100px; resize: vertical; }
        .lp-footer { background: #007f7f; color: rgba(255,255,255,.8); padding: 2rem 0; text-align: center; font-size: 0.85rem; }
        .lp-footer a { color: var(--lp-primary); text-decoration: none; }
        .success-message { background: #f0fdf4; color: #16a34a; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-weight: 700; text-align: center; }
        .lp-template-lead_generation .lp-hero { text-align:left; }
        .lp-template-lead_generation .lp-hero p { margin-left:0; }
        .lp-template-lead_generation .lp-hero .container { max-width:900px; }
        .lp-template-lead_generation .lp-form { border-top:4px solid var(--lp-primary); }
        .lp-template-lead_generation .lp-cta { max-width:760px; margin-left:auto; margin-right:auto; }
        .lp-template-lead_generation .lp-form { max-width:none; }
        .lp-template-corporate_logistics .lp-hero { padding:6rem 0; }
        .lp-template-corporate_logistics .lp-content { max-width:850px; margin:auto; }
        .lp-template-service_promo .lp-hero { text-align:left; }
        .lp-template-service_promo .lp-hero p { margin-left:0; }
        .lp-template-service_promo .lp-content { background:#f5f4ef; padding:2rem; border-radius:18px; }
        .lp-template-tracking_lead .lp-hero { padding:3.5rem 0; }
        .lp-template-tracking_lead .lp-cta { border:2px solid var(--lp-accent); background:#eef9f7; }
        .lp-template-tracking_lead .lp-form { background:#fff; }
        .lp-template-home_page .lp-hero { text-align:left; background:linear-gradient(135deg,#f5f4ef 0%,#fff 68%); color:#0d1b20; border-bottom:1px solid rgba(13,27,32,.08); }
        .lp-template-home_page .lp-hero .container { max-width:1100px; }
        .lp-template-home_page .lp-hero p { margin-left:0; color:#33444a; opacity:1; }
        .lp-template-home_page .lp-kicker { color:#007f7f; border-color:rgba(0,127,127,.25); }
        .lp-template-home_page .lp-btn-secondary { color:#007f7f; border-color:#007f7f; }
        .lp-template-home_page .lp-btn-secondary:hover { background:#eef9f7; }
        .lp-template-home_page .lp-choice-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin:2rem 0 3rem; }
        .lp-template-home_page .lp-choice { display:flex; flex-direction:column; gap:.45rem; padding:1.25rem; background:#fff; border:1px solid rgba(13,27,32,.1); border-radius:16px; box-shadow:0 10px 24px rgba(13,27,32,.06); }
        .lp-template-home_page .lp-choice i { color:var(--lp-primary); font-size:1.2rem; }
        .lp-template-home_page .lp-choice strong { color:#0d1b20; }
        .lp-template-home_page .lp-choice span { color:#56666c; font-size:.82rem; }
        .lp-template-services_page .lp-hero { text-align:left; }
        .lp-template-services_page .lp-hero p { margin-left:0; }
        .lp-template-services_page .lp-service-index { display:grid; grid-template-columns:repeat(4,1fr); gap:.8rem; margin:2rem 0 3rem; }
        .lp-template-services_page .lp-service-card { background:#fff; border:1px solid rgba(13,27,32,.1); border-radius:14px; padding:1.1rem; }
        .lp-template-services_page .lp-service-card i { color:var(--lp-accent); margin-bottom:.65rem; }
        .lp-template-services_page .lp-service-card strong { display:block; color:#0d1b20; font-size:.9rem; }
        .lp-template-services_page .lp-service-card span { display:block; color:#56666c; font-size:.76rem; margin-top:.25rem; }
        .lp-mid-cta { display:flex; justify-content:space-between; align-items:center; gap:1.5rem; padding:1.25rem 1.5rem; margin:2rem 0; border-radius:16px; background:#fff; border:1px solid rgba(0,127,127,.2); box-shadow:0 10px 28px rgba(13,27,32,.06); }
        .lp-mid-cta strong { color:#0d1b20; }
        .lp-mid-cta span { display:block; color:#56666c; font-size:.86rem; }
        .lp-mid-cta .lp-btn { padding:.7rem 1rem; font-size:.85rem; white-space:nowrap; }
        .lp-template-service_promo .lp-cta { display:grid; grid-template-columns:.8fr 1.2fr; gap:2rem; text-align:left; align-items:center; }
        .lp-template-service_promo .lp-cta h2, .lp-template-service_promo .lp-cta p { grid-column:1; }
        .lp-template-service_promo .lp-cta form { grid-column:2; grid-row:1 / span 2; }
        .lp-template-service_promo .lp-form { margin:0; max-width:none; }
        .lp-template-corporate_logistics .lp-cta { background:#eef9f7; border-color:rgba(0,127,127,.25); }
        .lp-template-corporate_logistics .lp-cta h2, .lp-template-corporate_logistics .lp-cta p { color:#0d1b20; }
        .lp-template-corporate_logistics .lp-form { background:#fff; color:#0d1b20; }
        .lp-template-lead_generation .lp-hero, .lp-template-service_promo .lp-hero { background:linear-gradient(135deg,#eef9f7 0%,#fff 100%); color:#0d1b20; }
        .lp-template-lead_generation .lp-kicker, .lp-template-service_promo .lp-kicker { color:#007f7f; border-color:rgba(0,127,127,.25); }
        .lp-template-lead_generation .lp-hero p, .lp-template-service_promo .lp-hero p { color:#56666c; opacity:1; }
        .lp-template-lead_generation .lp-btn-secondary, .lp-template-service_promo .lp-btn-secondary { color:#007f7f; border-color:#007f7f; }
        .lp-template-lead_generation .lp-btn-secondary:hover, .lp-template-service_promo .lp-btn-secondary:hover { background:#eef9f7; }
        @media(max-width:700px){.lp-template-service_promo .lp-cta{display:block}.lp-template-service_promo .lp-form{margin-top:1.5rem}.lp-mid-cta{align-items:flex-start;flex-direction:column}.lp-mid-cta .lp-btn{width:100%;text-align:center}.lp-btn-secondary{margin-left:0}.lp-gallery{grid-template-columns:1fr 1fr}.lp-gallery img{height:150px}.lp-benefits{grid-template-columns:1fr;margin-top:1.25rem}.lp-hero{padding:4.5rem 0}.lp-trust{align-items:flex-start;flex-direction:column;gap:.7rem}.lp-template-home_page .lp-choice-grid{grid-template-columns:1fr}.lp-template-services_page .lp-service-index{grid-template-columns:1fr 1fr}}
    </style>
</head>
<body class="lp-template-{{ $page->template ?: 'freight_quote' }}">
    <div class="lp-header">
        <div class="container">
            <div class="lp-logo"><img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight"></div>
            <div class="lp-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services') }}">Services</a>
                <a href="{{ route('quote') }}">Get Quote</a>
            </div>
        </div>
    </div>

    <div class="lp-hero" @if($page->hero_image_url) style="background-image:linear-gradient(rgba(13,27,32,.78),rgba(0,127,127,.78)),url('{{ asset('storage/'.ltrim($page->hero_image_url,'/')) }}')" @endif>
        <div class="container">
            <div class="lp-kicker">Forus Freight · {{ str_replace('_',' ', $page->template ?: 'freight quote') }}</div><h1>{{ $page->hero_title ?: $page->title }}</h1>
            <p>{{ $page->hero_text ?: Str::limit(strip_tags($page->content), 200) }}</p>
            <div class="lp-hero-actions"><a href="{{ $page->cta_url ?: '#form' }}" class="lp-btn"><i class="fas fa-paper-plane"></i> {{ $page->cta_label ?: 'Get a quote' }}</a><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('forus.company.phone')) }}?text={{ urlencode('Hi Forus Freight, I would like to discuss a freight enquiry.') }}" class="lp-btn-secondary" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a></div>
        </div>
    </div>

    <div class="container">
        @if($page->template === 'home_page')
        <section class="lp-choice-grid" aria-label="Choose a freight service path">
            <div class="lp-choice"><i class="fas fa-route"></i><strong>Move business cargo</strong><span>Road, air, and sea freight for imports, exports, and regional delivery.</span></div>
            <div class="lp-choice"><i class="fas fa-location-dot"></i><strong>Track a shipment</strong><span>Check the latest movement and status of an existing Forus Freight shipment.</span></div>
            <div class="lp-choice"><i class="fas fa-comments"></i><strong>Speak with the team</strong><span>Ask a route or cargo question on WhatsApp and get a practical next step.</span></div>
        </section>
        @elseif($page->template === 'services_page')
        <section class="lp-service-index" aria-label="Freight services">
            <div class="lp-service-card"><i class="fas fa-truck"></i><strong>Road freight</strong><span>Regional movement across Zambia and SADC.</span></div>
            <div class="lp-service-card"><i class="fas fa-plane"></i><strong>Air freight</strong><span>Urgent, small, and high-value cargo.</span></div>
            <div class="lp-service-card"><i class="fas fa-ship"></i><strong>Sea freight</strong><span>Cost-conscious larger volume imports.</span></div>
            <div class="lp-service-card"><i class="fas fa-file-circle-check"></i><strong>Customs and warehousing</strong><span>Documentation, clearance, and storage support.</span></div>
        </section>
        @endif
        <section class="lp-benefits" aria-label="Why choose Forus Freight">
            <div class="lp-benefit"><i class="fas fa-route"></i><strong>Routes planned around your cargo</strong><span>Clear options for road, air, sea, and regional freight.</span></div>
            <div class="lp-benefit"><i class="fas fa-file-circle-check"></i><strong>Practical shipment support</strong><span>Guidance from enquiry through documentation and delivery.</span></div>
            <div class="lp-benefit"><i class="fas fa-comments"></i><strong>A team you can reach</strong><span>Speak with Forus Freight by phone, WhatsApp, or email.</span></div>
        </section>
        <div class="lp-trust" aria-label="Forus Freight contact details"><span><i class="fas fa-location-dot"></i> Makeni, Lusaka, Zambia</span><span><i class="fas fa-phone"></i> {{ config('forus.company.phone') }}</span><span><i class="fas fa-shield-halved"></i> Clear, human support</span></div>
        @php($shareUrl = urlencode(url('/lp/'.$page->slug)))
        @php($shareTitle = urlencode(($page->hero_title ?: $page->title) . ' - Forus Freight'))
        <div class="lp-share" aria-label="Share this landing page"><span>Share this page</span><a class="share-wa" href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a><a class="share-li" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i> LinkedIn</a><a class="share-fb" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i> Facebook</a><a class="share-x" href="https://x.com/intent/post?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-x-twitter"></i> X</a></div>
        <div class="lp-content">
            {!! $page->content !!}
        </div>

        @if($page->gallery_images && count($page->gallery_images))
        <section class="lp-gallery" aria-label="Forus Freight service images">
            @foreach($page->gallery_images as $image)
                <figure><img src="{{ asset('storage/'.ltrim($image,'/')) }}" alt="Forus Freight logistics service"><figcaption>Forus Freight · dependable movement</figcaption></figure>
            @endforeach
        </section>
        @endif

        <div class="lp-mid-cta"><div><strong>Prefer a quick conversation?</strong><span>Chat with the Forus Freight team on WhatsApp.</span></div><a class="lp-btn" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('forus.company.phone')) }}?text={{ urlencode('Hi Forus Freight, I need help with a freight enquiry.') }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> Start chat</a></div>

        @if($page->show_form)
        <div class="lp-cta" id="form">
            @if($page->template === 'lead_generation')
                <h2>{{ $page->form_title ?: 'Let’s plan your next shipment' }}</h2><p>{{ $page->form_description ?: 'Share a few details and receive a response from our freight team.' }}</p>
            @elseif($page->template === 'corporate_logistics')
                <h2>{{ $page->form_title ?: 'Talk to a freight specialist' }}</h2><p>{{ $page->form_description ?: 'For recurring routes, supplier movements, and corporate logistics support.' }}</p>
            @elseif($page->template === 'service_promo')
                <h2>{{ $page->form_title ?: 'Get started with this service' }}</h2><p>{{ $page->form_description ?: 'Tell us about your cargo and we will prepare the right next step.' }}</p>
            @elseif($page->template === 'tracking_lead')
                <h2>{{ $page->form_title ?: 'Need help with a shipment?' }}</h2><p>{{ $page->form_description ?: 'Send us your details and our team will help you move forward.' }}</p>
            @elseif($page->template === 'home_page')
                <h2>{{ $page->form_title ?: 'Where should your shipment go?' }}</h2><p>{{ $page->form_description ?: 'Tell us what you are moving and our team will guide you to the right freight option.' }}</p>
            @elseif($page->template === 'services_page')
                <h2>{{ $page->form_title ?: 'Choose the right freight service' }}</h2><p>{{ $page->form_description ?: 'Share your route and cargo details and we will recommend the right service.' }}</p>
            @else
                <h2>{{ $page->form_title ?: 'Ready to ship?' }}</h2><p>{{ $page->form_description ?: 'Tell us what you are moving and where it needs to go.' }}</p>
            @endif

            @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
            @endif

            <form class="lp-form" action="{{ route('public.lp.submit', $page->slug) }}" method="POST">
                @csrf
                <h3>Get Your Free Quote</h3>
                @if(in_array('name', $page->form_fields ?: ['name','email','phone','message']))<div class="lp-form-group">
                    <label for="lp_name">Full Name *</label>
                    <input id="lp_name" type="text" name="name" required placeholder="John Banda">
                </div>@endif
                <div class="lp-form-group">
                    <label for="lp_email">Email Address *</label>
                    <input id="lp_email" type="email" name="email" required placeholder="john@company.com">
                </div>
                @foreach(['phone'=>'Phone Number','company'=>'Company','service'=>'Service needed','origin'=>'Origin','destination'=>'Destination'] as $field=>$label)
                @if(in_array($field, $page->form_fields ?: ['name','email','phone','message']))<div class="lp-form-group">
                    <label for="lp_{{ $field }}">{{ $label }}{{ in_array($field, $page->form_fields ?: []) ? ' *' : '' }}</label>
                    <input id="lp_{{ $field }}" type="{{ $field === 'phone' ? 'tel' : 'text' }}" name="{{ $field }}" {{ in_array($field, $page->form_fields ?: []) ? 'required' : '' }}>
                </div>@endif
                @endforeach
                @if(in_array('message', $page->form_fields ?: ['name','email','phone','message']))<div class="lp-form-group">
                    <label for="lp_message">What are you shipping?</label>
                    <textarea id="lp_message" name="message" required placeholder="Tell us about your cargo, route, and timeline..."></textarea>
                </div>@endif
                <div class="lp-form-group" style="display:flex; align-items:flex-start; gap:.6rem;">
                    <input type="checkbox" id="lp_consent" name="consent" required style="margin-top:.3rem; width:18px; height:18px; flex-shrink:0;">
                    <label for="lp_consent" style="font-weight:400; font-size:.85rem;">
                        I agree to Forus Freight processing this information to contact me about my request, per the <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a>. *
                    </label>
                </div>
                <button type="submit" class="lp-btn" style="width: 100%;"><i class="fas fa-paper-plane"></i> {{ $page->cta_label ?: 'Send enquiry' }}</button>
            </form>
        </div>
        @else
        <div class="lp-cta lp-no-form" id="form"><h2>{{ $page->cta_label ?: 'Start your freight enquiry' }}</h2><p>{{ $page->form_description ?: 'Choose the fastest way to speak with Forus Freight about your shipment.' }}</p><div class="lp-hero-actions"><a class="lp-btn" href="{{ $page->cta_url ?: 'https://wa.me/'.preg_replace('/[^0-9]/', '', config('forus.company.phone')) }}">{{ $page->cta_label ?: 'Continue' }}</a><a class="lp-btn-secondary" style="background:#007f7f;border-color:#007f7f" href="{{ route('quote') }}">Get a formal quote</a></div></div>
        @endif
    </div>

    <div class="lp-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Forus Freight. All rights reserved. <a href="{{ route('home') }}">Home</a> | <a href="{{ route('contact') }}">Contact</a></p>
        </div>
    </div>
    <script>
        (() => {
            const started = Date.now();
            let engaged = false;
            const sendEngagement = () => {
                if (engaged) return;
                engaged = true;
                const payload = JSON.stringify({_token: document.querySelector('meta[name="csrf-token"]').content, visit_id: {{ $visit->id }}, duration_seconds: Math.round((Date.now() - started) / 1000), bounced: false});
                navigator.sendBeacon('{{ route('public.lp.engagement', $page->slug) }}', new Blob([payload], {type: 'application/json'}));
            };
            window.addEventListener('scroll', () => { if (window.scrollY > 120) sendEngagement(); }, {once:true, passive:true});
            document.querySelectorAll('.lp-btn, .lp-btn-secondary').forEach(button => button.addEventListener('click', () => {
                const payload = JSON.stringify({_token: document.querySelector('meta[name="csrf-token"]').content, visit_id: {{ $visit->id }}, duration_seconds: Math.round((Date.now() - started) / 1000), bounced: false, event: 'cta_click', cta_type: button.textContent.trim().slice(0, 40)});
                navigator.sendBeacon('{{ route('public.lp.engagement', $page->slug) }}', new Blob([payload], {type: 'application/json'}));
            }));
            window.addEventListener('beforeunload', sendEngagement);
        })();
    </script>
</body>
</html>
