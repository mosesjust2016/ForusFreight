@extends('layouts.dashboard')

@section('title', 'Landing Pages - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .page-row { transition: all 0.2s; }
    .page-row:hover { background: #fcfdfe; }
    .page-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .page-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .page-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Landing Pages</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Track engagement across landing pages and conversion funnels.</p>
            <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem;">Public URL format: <code>/lp/{slug}</code> (e.g., /lp/promo-may-2025)</p>
        </div>
    </div>
</div>

<!-- Quick Create -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Create Landing Page</h2>
    <form action="{{ route('admin.crm.landing-pages.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="title" placeholder="Page title" required>
        <input type="text" name="slug" placeholder="URL slug" required>
        <input type="text" name="campaign_source" placeholder="UTM Source">
        <input type="text" name="campaign_medium" placeholder="UTM Medium">
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create</button>
    </form>
</div>

<!-- Pages List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Page</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Slug</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Views</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Submissions</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Conversion</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
            <tr class="page-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $page->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">Source: {{ $page->campaign_source ?? 'N/A' }} / Medium: {{ $page->campaign_medium ?? 'N/A' }}</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">/{{ $page->slug }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @php $statusColors = ['draft' => ['#f1f5f9','#475569'], 'published' => ['#f0fdf4','#16a34a'], 'archived' => ['#f3f4f6','#9ca3af']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$page->status][0] }}; color: {{ $statusColors[$page->status][1] }};">{{ ucfirst($page->status) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ number_format($page->views) }}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ number_format($page->submissions) }}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 900; color: #3b82f6;">{{ $page->conversion_rate }}%</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    @if($page->status === 'published')
                    <a href="{{ route('public.lp.page', $page->slug) }}" target="_blank" class="action-btn" style="margin-right: 0.25rem;" title="View public page"><i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i></a>
                    @endif
                    @if($page->status === 'draft')
                    <form action="{{ route('admin.crm.landing-pages.status', $page) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="published">
                        <button type="submit" class="action-btn" title="Publish" style="width: auto; padding: 0 0.75rem; font-weight: 800; color: var(--primary-green);"><i class="fas fa-upload"></i></button>
                    </form>
                    @elseif($page->status === 'published')
                    <form action="{{ route('admin.crm.landing-pages.status', $page) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="archived">
                        <button type="submit" class="action-btn" title="Archive" style="width: auto; padding: 0 0.75rem; font-weight: 800; color: #ef4444;"><i class="fas fa-archive"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-globe" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Landing Pages</p>
                    <p style="font-size: 0.85rem;">Create your first landing page to track engagement.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $pages->links() }}</div>
</div>
@endsection
