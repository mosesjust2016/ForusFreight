@extends('layouts.dashboard')

@section('title', 'CRM Home - Forus Freight')

@section('styles')
<style>
    .crm-home { max-width: 1440px; }
    .crm-hero { display:flex; justify-content:space-between; align-items:center; gap:1.25rem; margin-bottom:1.5rem; padding:1.5rem; border-radius:22px; background:linear-gradient(135deg,#102a2f 0%,#175451 100%); color:#fff; box-shadow:0 16px 36px rgba(16,42,47,.16); }
    .crm-hero h1 { margin:0 0 .35rem; font-size:2rem; letter-spacing:-.04em; }
    .crm-hero p { margin:0; color:rgba(255,255,255,.7); font-size:.9rem; }
    .crm-primary-actions { display:flex; gap:.6rem; flex-wrap:wrap; }
    .crm-primary-actions a { display:inline-flex; align-items:center; gap:.45rem; padding:.7rem .9rem; border-radius:10px; text-decoration:none; font-size:.78rem; font-weight:900; }
    .crm-primary-actions .primary { background:var(--orange,#ff6200); color:#102a2f; }
    .crm-primary-actions .secondary { background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.22); }
    .crm-stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
    .crm-stat { background:#fff; border:1px solid rgba(13,27,32,.08); border-radius:16px; padding:1.1rem; box-shadow:0 10px 24px rgba(13,27,32,.05); transition:transform .18s ease,box-shadow .18s ease; }
    .crm-stat:hover { transform:translateY(-3px); box-shadow:0 16px 30px rgba(13,27,32,.1); }
    .crm-stat-label { color:var(--text-gray); text-transform:uppercase; letter-spacing:.06em; font-size:.65rem; font-weight:900; }
    .crm-stat-value { margin-top:.35rem; font-size:1.55rem; font-weight:900; color:#102a2f; letter-spacing:-.03em; }
    .crm-stat a { display:inline-block; margin-top:.5rem; color:#147d72; font-size:.73rem; font-weight:800; text-decoration:none; }
    .crm-section-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
    .crm-queue { background:#fff; border:1px solid rgba(13,27,32,.08); border-radius:18px; padding:1.2rem; box-shadow:0 10px 24px rgba(13,27,32,.05); }
    .crm-queue-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:.8rem; }
    .crm-queue-header h2 { margin:0; font-size:1rem; }
    .crm-queue-header a { color:#147d72; text-decoration:none; font-size:.72rem; font-weight:900; }
    .crm-row { display:flex; justify-content:space-between; gap:1rem; align-items:center; padding:.85rem .5rem; margin:0 -.5rem; border-top:1px solid #eef2f1; text-decoration:none; color:inherit; border-radius:10px; }
    .crm-row:hover { background:#f7faf9; }
    .crm-row:first-of-type { border-top:0; }
    .crm-row-title { font-size:.82rem; font-weight:850; }
    .crm-row-meta { margin-top:.2rem; color:var(--text-gray); font-size:.72rem; }
    .crm-badge { border-radius:999px; padding:.3rem .55rem; background:#fff4dd; color:#a45b00; font-size:.65rem; font-weight:900; white-space:nowrap; }
    .crm-badge.urgent { background:#fee2e2; color:#b91c1c; }
    .crm-badge.ok { background:#dcfce7; color:#166534; }
    .crm-empty { color:var(--text-gray); font-size:.8rem; padding:.8rem 0; }
    @media (max-width:900px) { .crm-stat-grid { grid-template-columns:repeat(2,1fr); } .crm-section-grid { grid-template-columns:1fr; } .crm-hero { align-items:flex-start; flex-direction:column; } }
    @media (max-width:520px) { .crm-stat-grid { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
<div class="crm-home">
    @include('admin.crm._workspace-nav')

    <div class="crm-hero">
        <div>
            <h1>CRM Home</h1>
            <p>Your daily work queue: see what needs a response, follow-up, or owner.</p>
        </div>
        <div class="crm-primary-actions">
            <a class="primary" href="{{ route('admin.quote-requests') }}"><i class="fas fa-file-invoice-dollar"></i> Review quotes</a>
            <a class="secondary" href="{{ route('admin.crm.deals.create') }}"><i class="fas fa-plus"></i> New deal</a>
        </div>
    </div>

    <div class="crm-stat-grid">
        <div class="crm-stat"><div class="crm-stat-label">Contacts</div><div class="crm-stat-value">{{ number_format($stats['contacts']) }}</div><a href="{{ route('admin.crm.companies') }}">Open customers →</a></div>
        <div class="crm-stat"><div class="crm-stat-label">New leads</div><div class="crm-stat-value">{{ number_format($stats['leads']) }}</div><a href="{{ route('admin.crm.leads') }}">Assign leads →</a></div>
        <div class="crm-stat"><div class="crm-stat-label">Open pipeline</div><div class="crm-stat-value">{{ usd($stats['pipelineValue']) }}</div><a href="{{ route('admin.crm.pipeline') }}">{{ $stats['openDeals'] }} open deals →</a></div>
        <div class="crm-stat"><div class="crm-stat-label">Needs attention</div><div class="crm-stat-value">{{ number_format($stats['newQuotes'] + $stats['followUps'] + $stats['openTickets']) }}</div><a href="#work-queues">View work queues →</a></div>
    </div>

    <div id="work-queues" class="crm-section-grid">
        <section class="crm-queue">
            <div class="crm-queue-header"><h2><i class="fas fa-file-invoice-dollar" style="color:#ff6200;"></i> New quote requests</h2><a href="{{ route('admin.quote-requests') }}">View all</a></div>
            @forelse($quoteRequests as $quote)
                <a class="crm-row" href="{{ route('admin.quote-requests') }}">
                    <div><div class="crm-row-title">{{ $quote->full_name }} · {{ $quote->service_type }}</div><div class="crm-row-meta">{{ $quote->pickup }} → {{ $quote->delivery }} · {{ $quote->created_at->diffForHumans() }}</div></div>
                    <span class="crm-badge">New</span>
                </a>
            @empty <div class="crm-empty">No new quote requests.</div> @endforelse
        </section>

        <section class="crm-queue">
            <div class="crm-queue-header"><h2><i class="fas fa-list-check" style="color:#2563eb;"></i> Follow-ups due</h2><a href="{{ route('admin.crm.tasks') }}">View tasks</a></div>
            @forelse($followUps as $task)
                <a class="crm-row" href="{{ route('admin.crm.tasks') }}">
                    <div><div class="crm-row-title">{{ $task->title }}</div><div class="crm-row-meta">{{ $task->contact?->name ?? $task->deal?->title ?? 'No contact linked' }}</div></div>
                    <span class="crm-badge {{ $task->due_at?->isPast() ? 'urgent' : '' }}">{{ $task->due_at?->isPast() ? 'Overdue' : $task->due_at?->format('M j') }}</span>
                </a>
            @empty <div class="crm-empty">No follow-ups due today.</div> @endforelse
        </section>

        <section class="crm-queue">
            <div class="crm-queue-header"><h2><i class="fas fa-user-plus" style="color:#147d72;"></i> New leads</h2><a href="{{ route('admin.crm.leads') }}">View all</a></div>
            @forelse($leads as $lead)
                <a class="crm-row" href="{{ route('admin.crm.contacts.360', $lead) }}">
                    <div><div class="crm-row-title">{{ $lead->name }}</div><div class="crm-row-meta">{{ $lead->phone ?: $lead->email ?: 'No contact detail' }}</div></div>
                    <span class="crm-badge ok">Lead</span>
                </a>
            @empty <div class="crm-empty">No unassigned leads.</div> @endforelse
        </section>

        <section class="crm-queue">
            <div class="crm-queue-header"><h2><i class="fas fa-headset" style="color:#dc2626;"></i> Open support</h2><a href="{{ route('admin.crm.tickets') }}">View all</a></div>
            @forelse($tickets as $ticket)
                <a class="crm-row" href="{{ route('admin.crm.tickets.show', $ticket) }}">
                    <div><div class="crm-row-title">{{ $ticket->subject }}</div><div class="crm-row-meta">{{ $ticket->contact?->name ?? 'Unknown customer' }} · {{ $ticket->status }}</div></div>
                    <span class="crm-badge">{{ ucfirst($ticket->priority ?: 'Normal') }}</span>
                </a>
            @empty <div class="crm-empty">No open support tickets.</div> @endforelse
        </section>
    </div>
</div>
@endsection
