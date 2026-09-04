<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Forus Freight</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        header { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); color: white; padding: 3rem 0; text-align: center; }
        header h1 { font-size: 2.5rem; font-weight: 900; margin-bottom: 0.5rem; }
        header p { font-size: 1.1rem; opacity: 0.9; max-width: 600px; margin: 0 auto; }
        .search-bar { max-width: 600px; margin: -1.5rem auto 3rem; position: relative; z-index: 10; }
        .search-bar input { width: 100%; padding: 1rem 1.5rem; border: none; border-radius: 16px; font-size: 1rem; box-shadow: 0 10px 40px rgba(0,0,0,0.1); outline: none; }
        .search-bar button { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #22c55e; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 10px; cursor: pointer; font-weight: 700; }
        .categories { display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; margin-bottom: 2rem; }
        .category-pill { padding: 0.5rem 1rem; border-radius: 20px; background: white; color: #475569; font-weight: 700; font-size: 0.85rem; text-decoration: none; border: 2px solid #e2e8f0; transition: all 0.2s; }
        .category-pill:hover, .category-pill.active { background: #22c55e; color: white; border-color: #22c55e; }
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        .article-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; transition: all 0.2s; text-decoration: none; color: inherit; display: block; }
        .article-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-color: #22c55e; }
        .article-category { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #22c55e; margin-bottom: 0.5rem; }
        .article-title { font-weight: 800; font-size: 1.05rem; color: #1e293b; margin-bottom: 0.5rem; }
        .article-excerpt { font-size: 0.9rem; color: #64748b; line-height: 1.5; margin-bottom: 1rem; }
        .article-meta { display: flex; justify-content: space-between; font-size: 0.8rem; color: #94a3b8; font-weight: 600; }
        .popular-section { background: white; border-radius: 20px; padding: 2rem; margin-bottom: 3rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .popular-section h2 { font-size: 1.25rem; font-weight: 800; margin-bottom: 1.25rem; color: #1e293b; }
        .popular-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .popular-item { display: flex; align-items: center; gap: 1rem; padding: 0.75rem; border-radius: 10px; transition: background 0.2s; text-decoration: none; color: inherit; }
        .popular-item:hover { background: #f8fafc; }
        .popular-rank { width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #475569; }
        .popular-title { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
        .popular-views { margin-left: auto; font-size: 0.75rem; color: #94a3b8; font-weight: 700; }
        footer { background: #1e293b; color: #94a3b8; padding: 2rem 0; text-align: center; font-size: 0.85rem; }
        footer a { color: #22c55e; text-decoration: none; }
        .empty-state { text-align: center; padding: 4rem 0; color: #94a3b8; }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; }
        .pagination { display: flex; justify-content: center; margin-top: 2rem; }
        .pagination a, .pagination span { padding: 0.5rem 1rem; margin: 0 0.25rem; border-radius: 8px; background: white; color: #475569; text-decoration: none; font-weight: 700; }
        .pagination .active { background: #22c55e; color: white; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-book-open"></i> Help Center</h1>
            <p>Find answers to common questions about shipping, customs, tracking, and more.</p>
        </div>
    </header>

    <div class="container">
        <div class="search-bar">
            <form action="{{ route('public.kb.index') }}" method="GET">
                <input type="text" name="q" placeholder="Search articles..." value="{{ request('q') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="categories">
            <a href="{{ route('public.kb.index') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
            <a href="{{ route('public.kb.index', ['category' => $cat]) }}" class="category-pill {{ request('category') === $cat ? 'active' : '' }}">{{ ucfirst($cat) }}</a>
            @endforeach
        </div>

        @if($popular->count() > 0 && !request('q') && !request('category'))
        <div class="popular-section">
            <h2><i class="fas fa-fire" style="color: #f59e0b;"></i> Most Popular</h2>
            <div class="popular-list">
                @foreach($popular as $idx => $article)
                <a href="{{ route('public.kb.article', $article->slug) }}" class="popular-item">
                    <div class="popular-rank">{{ $idx + 1 }}</div>
                    <div class="popular-title">{{ $article->title }}</div>
                    <div class="popular-views"><i class="fas fa-eye"></i> {{ $article->views }}</div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($articles->count() > 0)
        <div class="articles-grid">
            @foreach($articles as $article)
            <a href="{{ route('public.kb.article', $article->slug) }}" class="article-card">
                <div class="article-category">{{ ucfirst($article->category) }}</div>
                <div class="article-title">{{ $article->title }}</div>
                <div class="article-excerpt">{{ Str::limit(strip_tags($article->content), 120) }}</div>
                <div class="article-meta">
                    <span><i class="fas fa-eye"></i> {{ $article->views }} views</span>
                    <span>{{ $article->created_at->diffForHumans() }}</span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="pagination">{{ $articles->links() }}</div>
        @else
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <p style="font-weight: 800; font-size: 1.1rem;">No articles found</p>
            <p>Try a different search term or browse all categories.</p>
        </div>
        @endif
    </div>

    <footer>
        <div class="container">
            <p>Need more help? <a href="{{ route('contact') }}">Contact our support team</a> or <a href="{{ route('home') }}">visit our homepage</a>.</p>
            <p style="margin-top: 0.5rem;">&copy; {{ date('Y') }} Forus Freight. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
