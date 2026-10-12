@extends('layouts.dashboard')

@section('title', 'Landing Pages - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); overflow-x: auto; }
    .page-row { transition: all 0.2s; }
    .page-row:hover { background: #fcfdfe; }
    .page-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .page-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .page-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .page-actions { display:flex; align-items:center; justify-content:flex-end; gap:.35rem; flex-wrap:nowrap; white-space:nowrap; }
    .page-actions form { display:inline-flex !important; margin:0; }
    .page-actions .action-btn { flex:0 0 auto; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
@include('admin.crm._workspace-nav')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Landing Pages</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Track engagement across landing pages and conversion funnels.</p>
            <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem;">Build hero sections, images, content, CTAs, forms, colors, and campaign tracking.</p>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem"><a href="{{ route('admin.crm.landing-pages.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Create landing page</a></div>

<div class="crm-grid landing-workflow" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;padding:1.25rem">
    <div><span>01</span><strong>Choose a template</strong><small>Start with the campaign goal and page structure.</small></div>
    <div><span>02</span><strong>Build the experience</strong><small>Add content, image, CTA, form fields, and tracking.</small></div>
    <div><span>03</span><strong>Preview and publish</strong><small>Check the page, then publish when it is ready.</small></div>
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
                <td style="padding: 1.25rem 1rem; text-align: right;"><div class="page-actions">
                    <a href="{{ route('admin.crm.landing-pages.edit', $page) }}" class="action-btn" style="margin-right: 0.25rem;" title="Edit landing page"><i class="fas fa-pen"></i></a>
                    <a href="{{ route('admin.crm.landing-pages.preview', $page) }}" target="_blank" class="action-btn" style="margin-right: 0.25rem;" title="Preview"><i class="fas fa-eye"></i></a>
                    @if($page->status === 'published')
                    <a href="{{ route('public.lp.page', $page->slug) }}" target="_blank" class="action-btn" style="margin-right: 0.25rem;" title="View public page"><i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i></a>
                    @endif
                    <form action="{{ route('admin.crm.landing-pages.destroy', $page) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this landing page permanently?')">@csrf @method('DELETE')<button type="submit" class="action-btn" title="Delete" style="color:#dc2626"><i class="fas fa-trash"></i></button></form>
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
                    </div>
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
    @if($pages->hasPages())
    <nav class="landing-pagination" aria-label="Landing pages pagination">
        @if($pages->onFirstPage())<span class="pagination-disabled"><i class="fas fa-chevron-left"></i> Previous</span>@else<a href="{{ $pages->previousPageUrl() }}"><i class="fas fa-chevron-left"></i> Previous</a>@endif
        <span class="pagination-status">Page {{ $pages->currentPage() }} of {{ $pages->lastPage() }}</span>
        @if($pages->hasMorePages())<a href="{{ $pages->nextPageUrl() }}">Next <i class="fas fa-chevron-right"></i></a>@else<span class="pagination-disabled">Next <i class="fas fa-chevron-right"></i></span>@endif
    </nav>
    @endif
</div>
<style>
.landing-workflow{background:linear-gradient(120deg,#fff,#eef9f7)}.landing-workflow div{display:flex;flex-direction:column;gap:.25rem;padding:.35rem .6rem}.landing-workflow span{color:#ff6200;font-size:.7rem;font-weight:900}.landing-workflow strong{color:#0d1b20;font-size:.85rem}.landing-workflow small{color:#56666c;font-size:.75rem;line-height:1.45}.landing-pagination{display:flex;align-items:center;justify-content:flex-end;gap:.65rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--line)}.landing-pagination a,.pagination-disabled,.pagination-status{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem .8rem;border-radius:9px;font-size:.78rem;font-weight:800}.landing-pagination a{background:#fff;border:1px solid var(--line);color:var(--ink);text-decoration:none}.landing-pagination a:hover{border-color:var(--primary-green);color:var(--primary-green)}.pagination-disabled{color:#a0abad;background:#f5f7f6}.pagination-status{color:var(--text-gray)}@media(max-width:700px){.landing-workflow{grid-template-columns:1fr!important}.page-actions{justify-content:flex-start}.landing-pagination{justify-content:center}}
</style>
@endsection
