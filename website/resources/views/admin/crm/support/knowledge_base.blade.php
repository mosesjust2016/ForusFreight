@extends('layouts.dashboard')

@section('title', 'Knowledge Base - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); overflow-x: auto; }
    .article-row { transition: all 0.2s; }
    .article-row:hover { background: #fcfdfe; }
    .article-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .article-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .article-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.9rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select, .form-inline textarea { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Knowledge Base</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Self-service articles for faster resolution and reduced ticket volume.</p>
        </div>
        <a href="{{ route('public.kb.index') }}" target="_blank" class="btn-primary" style="text-decoration: none;"><i class="fas fa-external-link-alt"></i> View Public Site</a>
    </div>
</div>

<!-- Category Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.knowledge-base') }}" class="{{ !request('category') && !request('status') ? 'active' : '' }}">All</a>
    @foreach($categories as $cat)
        <a href="{{ route('admin.crm.knowledge-base', ['category' => $cat]) }}" class="{{ request('category') === $cat ? 'active' : '' }}">{{ ucfirst($cat) }}</a>
    @endforeach
    <a href="{{ route('admin.crm.knowledge-base', ['status' => 'draft']) }}" class="{{ request('status') === 'draft' ? 'active' : '' }}">Drafts</a>
</div>

<!-- Quick Create -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Publish Article</h2>
    <form action="{{ route('admin.crm.knowledge-base.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="title" placeholder="Article title" required>
        <input type="text" name="slug" placeholder="URL slug" required>
        <select name="category" required>
            <option value="general">General</option>
            <option value="billing">Billing</option>
            <option value="technical">Technical</option>
            <option value="shipping">Shipping</option>
            <option value="account">Account</option>
        </select>
        <select name="status" required>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #475569;">
            <input type="checkbox" name="is_internal" value="1"> Internal Only
        </label>
        <textarea name="content" placeholder="Article content..." rows="2" style="resize: vertical; min-width: 300px;"></textarea>
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Publish</button>
    </form>
</div>

<!-- Articles List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Article</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Category</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Views</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Author</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
            <tr class="article-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $article->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">/{{ $article->slug }} @if($article->is_internal) &middot; <span style="color: #ef4444;">Internal</span> @endif</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700; text-transform: capitalize;">{{ $article->category }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @php $statusColors = ['draft' => ['#f1f5f9','#475569'], 'published' => ['#f0fdf4','#16a34a'], 'archived' => ['#f3f4f6','#9ca3af']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$article->status][0] }}; color: {{ $statusColors[$article->status][1] }};">{{ ucfirst($article->status) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ number_format($article->views) }}</td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $article->author->name }}</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    @if($article->status === 'published' && !$article->is_internal)
                    <a href="{{ route('public.kb.article', $article->slug) }}" target="_blank" class="action-btn" style="margin-right: 0.25rem;" title="View public article"><i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i></a>
                    @endif
                    <form action="{{ route('admin.crm.knowledge-base.update', $article) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $article->title }}">
                        <input type="hidden" name="content" value="{{ $article->content }}">
                        <input type="hidden" name="category" value="{{ $article->category }}">
                        <select name="status" onchange="this.form.submit()" style="padding: 0.25rem 0.5rem; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                            <option value="draft" {{ $article->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $article->status === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ $article->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-book-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Articles Found</p>
                    <p style="font-size: 0.85rem;">Build your knowledge base by publishing articles.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $articles->links() }}</div>
</div>
@endsection
