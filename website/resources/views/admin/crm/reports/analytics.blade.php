@extends('layouts.dashboard')

@section('title', 'Analytics - CRM - Forus Freight')

@section('styles')
<style>
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.9rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .bar-chart { display: flex; align-items: flex-end; gap: 0.5rem; height: 150px; padding-top: 1rem; }
    .bar { flex: 1; border-radius: 6px 6px 0 0; min-width: 20px; transition: all 0.3s; }
    .agent-row { transition: all 0.2s; }
    .agent-row:hover { background: #fcfdfe; }
    .agent-row td { padding: 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .progress-bar { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--primary-green); border-radius: 10px; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">CRM Analytics</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Business intelligence for customer behavior and pipeline trends.</p>
        </div>
    </div>
</div>

<!-- Period Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.analytics', ['period' => 'week']) }}" class="{{ $period === 'week' ? 'active' : '' }}">Last 7 Days</a>
    <a href="{{ route('admin.crm.analytics', ['period' => 'month']) }}" class="{{ $period === 'month' ? 'active' : '' }}">Last 30 Days</a>
    <a href="{{ route('admin.crm.analytics', ['period' => 'quarter']) }}" class="{{ $period === 'quarter' ? 'active' : '' }}">Last Quarter</a>
    <a href="{{ route('admin.crm.analytics', ['period' => 'year']) }}" class="{{ $period === 'year' ? 'active' : '' }}">Last Year</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Deals by Stage -->
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-funnel-dollar" style="color: #3b82f6;"></i> Deals by Stage</h2>
        @php $maxDeals = $dealsByStage->max('deals_count') ?: 1; @endphp
        <div class="bar-chart">
            @foreach($dealsByStage as $stage)
            <div style="display: flex; flex-direction: column; align-items: center; flex: 1; gap: 0.5rem;">
                <div class="bar" style="height: {{ ($stage->deals_count / $maxDeals) * 100 }}%; background: {{ $stage->color }};"></div>
                <div style="font-size: 0.7rem; font-weight: 800; color: var(--text-gray); text-align: center;">{{ $stage->name }}</div>
                <div style="font-size: 0.75rem; font-weight: 900; color: var(--text-dark);">{{ $stage->deals_count }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Contact Growth -->
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-user-plus" style="color: var(--primary-green);"></i> Contact Growth</h2>
        @php $maxGrowth = $contactGrowth->max('count') ?: 1; @endphp
        <div class="bar-chart">
            @foreach($contactGrowth as $day)
            <div style="display: flex; flex-direction: column; align-items: center; flex: 1; gap: 0.5rem;">
                <div class="bar" style="height: {{ ($day->count / $maxGrowth) * 100 }}%; background: var(--primary-green);"></div>
                <div style="font-size: 0.65rem; font-weight: 800; color: var(--text-gray); text-align: center;">{{ \Illuminate\Support\Carbon::parse($day->date)->format('M d') }}</div>
                <div style="font-size: 0.75rem; font-weight: 900; color: var(--text-dark);">{{ $day->count }}</div>
            </div>
            @endforeach
        </div>
        @if($contactGrowth->isEmpty())
        <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No new contacts in this period.</p>
        @endif
    </div>
</div>

<!-- Agent Performance -->
<div class="info-card" style="margin-bottom: 2rem;">
    <h2 class="section-title"><i class="fas fa-users-viewfinder" style="color: #8b5cf6;"></i> Agent Performance</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Agent</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Deals</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Pipeline Value</th>
                <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Performance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agentPerformance as $agent)
            <tr class="agent-row">
                <td style="padding: 0.75rem 0; font-weight: 800; color: var(--text-dark);">{{ $agent->name }}</td>
                <td style="padding: 0.75rem 0; font-weight: 700; color: #475569;">{{ $agent->deals_count ?? 0 }}</td>
                <td style="padding: 0.75rem 0; font-weight: 900; color: var(--text-dark);">{{ number_format($agent->deals_value ?? 0, 2) }} ZMW</td>
                <td style="padding: 0.75rem 0; width: 200px;">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ min(100, ($agent->deals_value ?? 0) / 100000 * 100) }}%;"></div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">No agent data for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Tickets by Status -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-headset" style="color: #ef4444;"></i> Tickets by Status</h2>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($ticketsByStatus as $status => $count)
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-dark); text-transform: capitalize;">{{ str_replace('_', ' ', $status) }}</div>
                <div style="font-weight: 900; color: #475569;">{{ $count }}</div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ min(100, ($count / max(1, $ticketsByStatus->sum())) * 100) }}%; background: {{ $status === 'resolved' || $status === 'closed' ? '#16a34a' : ($status === 'open' ? '#ef4444' : '#f59e0b') }};"></div>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No ticket data.</p>
            @endforelse
        </div>
    </div>

    <!-- Campaigns -->
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-bullhorn" style="color: #f59e0b;"></i> Campaign Performance</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Campaign</th>
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Leads</th>
                    <th style="padding: 0.75rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Conv.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                <tr class="agent-row">
                    <td style="padding: 0.75rem 0; font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $campaign->name }}</td>
                    <td style="padding: 0.75rem 0; font-weight: 700; color: #475569;">{{ $campaign->leads_generated }}</td>
                    <td style="padding: 0.75rem 0; font-weight: 900; color: var(--primary-green);">{{ $campaign->conversions }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">No campaigns in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
