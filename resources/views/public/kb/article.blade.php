<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Help Center - Forus Freight</title>
    <meta name="description" content="{{ Str::limit(strip_tags($article->content), 160) }}">
    <link rel="canonical" href="{{ route('public.kb.article', $article->slug) }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Forus Freight">
    <meta property="og:title" content="{{ $article->title }} - Forus Freight">
    <meta property="og:description" content="{{ Str::limit(strip_tags($article->content), 160) }}">
    <meta property="og:url" content="{{ route('public.kb.article', $article->slug) }}">
    <meta property="og:image" content="{{ asset('images/og-default.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->title }} - Forus Freight">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($article->content), 160) }}">
    @php($articleSchema = ['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $article->title, 'description' => Str::limit(strip_tags($article->content), 160), 'datePublished' => optional($article->created_at)->toIso8601String(), 'dateModified' => optional($article->updated_at)->toIso8601String(), 'author' => ['@type' => 'Organization', 'name' => 'Forus Freight'], 'publisher' => ['@type' => 'Organization', 'name' => 'Forus Freight', 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo-transparent.png')]], 'mainEntityOfPage' => route('public.kb.article', $article->slug)])
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Archivo:wdth,wght@100..125,500..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; background: #f5f4ef; color: #0d1b20; line-height: 1.7; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 1.5rem; }
        header { background: linear-gradient(135deg, #007f7f 0%, #00a6a6 100%); color: white; padding: 2rem 0; }
        header .container { display: flex; justify-content: space-between; align-items: center; }
        header a { color: white; text-decoration: none; font-weight: 700; }
        header a:hover { opacity: 0.8; }
        .breadcrumb { font-size: 0.85rem; opacity: 0.9; }
        .article-header { background: white; padding: 3rem 0 2rem; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; }
        .article-category { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 6px; background: #eef9f7; color: #007f7f; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem; }
        .article-title { font-size: 2rem; font-weight: 900; color: #1e293b; margin-bottom: 1rem; line-height: 1.3; }
        .article-meta { display: flex; gap: 1.5rem; font-size: 0.85rem; color: #94a3b8; font-weight: 600; }
        .share-row { display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; margin-top:1.25rem; }
        .share-row span { color:#64748b; font-size:.8rem; font-weight:800; }
        .share-row a { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .65rem; border-radius:8px; color:#fff; font-size:.75rem; font-weight:800; text-decoration:none; }
        .share-wa { background:#16a34a; } .share-li { background:#0a66c2; } .share-fb { background:#1877f2; } .share-x { background:#111827; }
        .article-content { background: white; border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); font-size: 1.05rem; }
        .article-content h2 { font-family:'Archivo','Inter',sans-serif; font-size: 1.4rem; font-weight: 800; margin: 2rem 0 1rem; color: #005e5e; }
        .article-content h3 { font-family:'Archivo','Inter',sans-serif; font-size: 1.15rem; font-weight: 800; margin: 1.5rem 0 0.75rem; color: #33444a; }
        .article-content p { margin-bottom: 1rem; color: #33444a; }
        .article-content ul, .article-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
        .article-content li { margin-bottom: 0.5rem; }
        .article-content a { color: #007f7f; font-weight:700; }
        .article-content blockquote { border-left: 4px solid #ff6200; padding-left: 1rem; margin: 1.5rem 0; color: #56666c; font-style: italic; }
        .article-content code { background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.9em; }
        .article-content pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto; }
        .related-section { margin-bottom: 3rem; }
        .related-section h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .related-card { background: white; border-radius: 12px; padding: 1.25rem; text-decoration: none; color: inherit; border: 1px solid #f1f5f9; transition: all 0.2s; }
        .related-card:hover { border-color: #22c55e; transform: translateY(-2px); }
        .related-card-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .related-card-meta { font-size: 0.75rem; color: #94a3b8; }
        .feedback-box { background: white; border-radius: 16px; padding: 2rem; text-align: center; margin-bottom: 3rem; }
        .feedback-box h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem; }
        .feedback-buttons { display: flex; gap: 1rem; justify-content: center; }
        .feedback-btn { padding: 0.75rem 1.5rem; border-radius: 10px; border: 2px solid #e2e8f0; background: white; font-weight: 700; cursor: pointer; transition: all 0.2s; }
        .feedback-btn:hover { border-color: #22c55e; background: #f0fdf4; }
        footer { background: #007f7f; color: rgba(255,255,255,.8); padding: 2rem 0; text-align: center; font-size: 0.85rem; }
        footer a { color: #ffb38b; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="{{ route('public.kb.index') }}"><i class="fas fa-arrow-left"></i> Back to Help Center</a>
            <div class="breadcrumb">Forus Freight &rsaquo; Help Center</div>
        </div>
    </header>

    <div class="article-header">
        <div class="container">
            <div class="article-category">{{ ucfirst($article->category) }}</div>
            <h1 class="article-title">{{ $article->title }}</h1>
            <div class="article-meta">
                <span><i class="fas fa-eye"></i> {{ $article->views }} views</span>
                <span><i class="fas fa-clock"></i> {{ $article->created_at->format('M d, Y') }}</span>
                <span><i class="fas fa-user"></i> {{ $article->author?->name ?? 'Support Team' }}</span>
            </div>
            @php($shareUrl = urlencode(route('public.kb.article', $article->slug)))
            @php($shareTitle = urlencode($article->title . ' - Forus Freight'))
            <div class="share-row" aria-label="Share this guide"><span>Share this guide</span><a class="share-wa" href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a><a class="share-li" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i> LinkedIn</a><a class="share-fb" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i> Facebook</a><a class="share-x" href="https://x.com/intent/post?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" rel="noopener"><i class="fab fa-x-twitter"></i> X</a></div>
        </div>
    </div>

    <div class="container">
        <div class="article-content">
            {!! $article->content !!}
        </div>

        @if($related->count() > 0)
        <div class="related-section">
            <h3><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Related Articles</h3>
            <div class="related-grid">
                @foreach($related as $rel)
                <a href="{{ route('public.kb.article', $rel->slug) }}" class="related-card">
                    <div class="related-card-title">{{ $rel->title }}</div>
                    <div class="related-card-meta">{{ ucfirst($rel->category) }} &middot; {{ $rel->views }} views</div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="feedback-box">
            <h3>Was this article helpful?</h3>
            <div class="feedback-buttons">
                <button class="feedback-btn" onclick="alert('Thank you for your feedback!')"><i class="fas fa-thumbs-up" style="color: #16a34a;"></i> Yes, it helped</button>
                <button class="feedback-btn" onclick="alert('Thank you for your feedback! We will improve this article.')"><i class="fas fa-thumbs-down" style="color: #ef4444;"></i> No, I need more help</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>Still have questions? <a href="{{ route('contact') }}">Contact our support team</a></p>
            <p style="margin-top: 0.5rem;">&copy; {{ date('Y') }} Forus Freight. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
