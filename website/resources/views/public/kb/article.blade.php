<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Help Center - Forus Freight</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.7; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 1.5rem; }
        header { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); color: white; padding: 2rem 0; }
        header .container { display: flex; justify-content: space-between; align-items: center; }
        header a { color: white; text-decoration: none; font-weight: 700; }
        header a:hover { opacity: 0.8; }
        .breadcrumb { font-size: 0.85rem; opacity: 0.9; }
        .article-header { background: white; padding: 3rem 0 2rem; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; }
        .article-category { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 6px; background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem; }
        .article-title { font-size: 2rem; font-weight: 900; color: #1e293b; margin-bottom: 1rem; line-height: 1.3; }
        .article-meta { display: flex; gap: 1.5rem; font-size: 0.85rem; color: #94a3b8; font-weight: 600; }
        .article-content { background: white; border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); font-size: 1.05rem; }
        .article-content h2 { font-size: 1.4rem; font-weight: 800; margin: 2rem 0 1rem; color: #1e293b; }
        .article-content h3 { font-size: 1.15rem; font-weight: 800; margin: 1.5rem 0 0.75rem; color: #334155; }
        .article-content p { margin-bottom: 1rem; color: #475569; }
        .article-content ul, .article-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
        .article-content li { margin-bottom: 0.5rem; }
        .article-content a { color: #22c55e; }
        .article-content blockquote { border-left: 4px solid #22c55e; padding-left: 1rem; margin: 1.5rem 0; color: #64748b; font-style: italic; }
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
        footer { background: #1e293b; color: #94a3b8; padding: 2rem 0; text-align: center; font-size: 0.85rem; }
        footer a { color: #22c55e; text-decoration: none; }
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
