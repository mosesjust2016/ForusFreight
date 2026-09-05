@extends('layouts.dashboard')

@section('title', 'CRM Dashboard - Forus Freight')

@section('styles')
<style>
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); overflow-x: auto; }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .insight-card { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 1rem; }
    .insight-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .deal-row { transition: all 0.2s; }
    .deal-row:hover { background: #fcfdfe; }
    .deal-row td { padding: 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
        div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: 1fr !important; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">CRM Dashboard</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Unified view of contacts, deals, tasks, and support health.</p>
        </div>
        <div style="background: var(--primary-green); color: white; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.8rem;">
            AI INSIGHTS ACTIVE
        </div>
    </div>
</div>

<!-- Top Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Total Contacts</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $totalContacts }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Companies</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $totalCompanies }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Open Pipeline</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">{{ number_format($pipelineValue, 2) }} ZMW</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Won Revenue</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ number_format($wonRevenue, 2) }} ZMW</div>
    </div>
</div>

<!-- Secondary Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Open Deals</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $openDeals }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Pending Tasks</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #f59e0b;">{{ $pendingTasks }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Overdue Tasks</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444;">{{ $overdueTasks }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Open Tickets</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #dc2626;">{{ $openTickets }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- AI Insights -->
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-robot" style="color: #8b5cf6;"></i> AI Insights</h2>
        @forelse($insights as $insight)
        @php
            $bgColors = ['warning' => '#fff8e1', 'danger' => '#fef2f2', 'info' => '#e3f2fd', 'neutral' => '#f8fafc', 'success' => '#f0fdf4'];
            $iconColors = ['warning' => '#f59e0b', 'danger' => '#ef4444', 'info' => '#1e88e5', 'neutral' => '#94a3b8', 'success' => '#16a34a'];
        @endphp
        <div class="insight-card" style="background: {{ $bgColors[$insight['type']] }};">
            <div class="insight-icon" style="background: {{ $iconColors[$insight['type']] }}20; color: {{ $iconColors[$insight['type']] }};">
                <i class="fas {{ $insight['icon'] }}"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.25rem;">{{ $insight['title'] }}</div>
                <div style="font-size: 0.85rem; color: #475569; font-weight: 600;">{{ $insight['message'] }}</div>
            </div>
        </div>
        @empty
        <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No insights at the moment. Everything looks good!</p>
        @endforelse
    </div>

    <!-- Recent Deals -->
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-handshake" style="color: var(--primary-green);"></i> Recent Deals</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Deal</th>
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Value</th>
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Stage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDeals as $deal)
                <tr class="deal-row">
                    <td style="padding: 0.75rem 0;">
                        <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $deal->title }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $deal->contact?->name ?? 'No contact' }}</div>
                    </td>
                    <td style="padding: 0.75rem 0; font-weight: 700; color: #475569;">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</td>
                    <td style="padding: 0.75rem 0;">
                        <span class="status-badge" style="background: {{ $deal->stage->color }}20; color: {{ $deal->stage->color }};">{{ $deal->stage->name }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">No recent deals.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Tickets -->
<div class="info-card" style="margin-bottom: 2rem;">
    <h2 class="section-title"><i class="fas fa-headset" style="color: #ef4444;"></i> Recent Support Tickets</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Ticket</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Contact</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Created</th>
                <th style="text-align: right; padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTickets as $ticket)
            <tr class="deal-row">
                <td style="padding: 0.75rem 0;">
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">#{{ $ticket->ticket_number }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $ticket->subject }}</div>
                </td>
                <td style="padding: 0.75rem 0; font-weight: 700; color: #475569;">{{ $ticket->contact?->name ?? 'N/A' }}</td>
                <td style="padding: 0.75rem 0;">
                    @php $statusColors = ['open' => ['#fef2f2','#ef4444'], 'in_progress' => ['#fff8e1','#f59e0b'], 'waiting' => ['#e3f2fd','#1e88e5'], 'resolved' => ['#f0fdf4','#16a34a'], 'closed' => ['#f1f5f9','#475569']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$ticket->status][0] }}; color: {{ $statusColors[$ticket->status][1] }};">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                </td>
                <td style="padding: 0.75rem 0; font-weight: 700; color: #475569;">{{ $ticket->created_at->diffForHumans() }}</td>
                <td style="padding: 0.75rem 0; text-align: right;">
                    <a href="{{ route('admin.crm.tickets.show', $ticket) }}" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">No recent tickets.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
