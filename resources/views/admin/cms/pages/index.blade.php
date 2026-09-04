@extends('layouts.dashboard')

@section('title', 'Website CMS - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .page-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow); border-left: 4px solid #22c55e; transition: all 0.2s; }
    .page-card:hover { transform: translateY(-2px); }
    .page-card.draft { border-left-color: #f59e0b; }
    .status-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
    .section-editor { background: #f8fafc; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; }
    .section-header { font-weight: 800; font-size: 0.85rem; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.75rem; }
    .form-control { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; width: 100%; margin-bottom: 0.5rem; }
    .form-control:focus { border-color: #22c55e; }
    textarea.form-control { min-height: 80px; resize: vertical; }
    .row-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .row-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Website CMS</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Edit your website pages without touching code.</p>
        </div>
        <a href="{{ route('admin.cms.pages.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Page</a>
    </div>
</div>

<div class="crm-grid">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        @forelse($pages as $page)
        <div class="page-card {{ $page->status === 'draft' ? 'draft' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 1rem; color: var(--text-dark);">{{ $page->title }}</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">/{{ $page->slug }}</div>
                </div>
                <span class="status-badge" style="background: {{ $page->status === 'published' ? '#f0fdf4' : '#fff8e1' }}; color: {{ $page->status === 'published' ? '#16a34a' : '#f59e0b' }};">
                    <i class="fas fa-circle" style="font-size: 0.4rem;"></i> {{ ucfirst($page->status) }}
                </span>
            </div>
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1rem;">
                <i class="fas fa-clock"></i> {{ $page->updated_at->diffForHumans() }}
                @if($page->editor) by {{ $page->editor->name }} @endif
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.cms.pages.edit', $page) }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none; font-size: 0.8rem;"><i class="fas fa-pen"></i> Edit</a>
                @if($page->status === 'published')
                <a href="{{ url('/') }}@if($page->slug !== 'home')/{{ $page->slug }}@endif" target="_blank" class="btn-secondary" style="text-align: center; text-decoration: none; font-size: 0.8rem;"><i class="fas fa-eye"></i></a>
                @endif
                @if(!in_array($page->slug, ['home', 'about', 'services', 'contact', 'terms']))
                <form action="{{ route('admin.cms.pages.destroy', $page) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this page?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-secondary" style="background: #fef2f2; color: #ef4444; font-size: 0.8rem;"><i class="fas fa-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #94a3b8;">
            <i class="fas fa-file-lines" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            <p style="font-weight: 800; font-size: 1rem;">No pages yet</p>
            <p style="font-size: 0.85rem;">Create your first CMS page.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
