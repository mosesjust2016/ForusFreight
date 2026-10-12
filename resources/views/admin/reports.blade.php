@extends('layouts.dashboard')

@section('title', 'System Reports & Analytics - Forus Freight')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .report-card {
        background: white;
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 4px 16px rgba(13,27,32,.05);
        border: 1px solid var(--line);
        transition: all 0.3s;
    }

    .report-card:hover {
        box-shadow: 0 8px 24px rgba(13,27,32,.08);
    }

    .report-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .report-value {
        font-size: 1.35rem;
        font-weight: 900;
        color: var(--text-dark);
    }

    .chart-container {
        background: white;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 4px 16px rgba(13,27,32,.05);
        border: 1px solid var(--line);
        margin-bottom: 1.25rem;
        position: relative;
        min-width: 0;
    }

    .log-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }

    .log-row {
        background: #f8fafc;
        border-radius: 12px;
    }

    .log-row td {
        padding: 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
    }

    .log-row td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .log-row td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .type-pill {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .report-nav { position: sticky; top: 0; z-index: 10; display: flex; gap: .35rem; overflow-x: auto; padding: .5rem; margin-bottom: 1.25rem; background: rgba(245,244,239,.94); border: 1px solid var(--line); border-radius: 12px; backdrop-filter: blur(8px); }
    .report-nav a { white-space: nowrap; padding: .45rem .75rem; border-radius: 8px; color: var(--ink-2); font-size: .78rem; font-weight: 700; text-decoration: none; }
    .report-nav a:hover { background: var(--ink); color: #fff; }
    .report-nav a.is-active { background: var(--orange); color: var(--ink); }
    .report-panel { display: none !important; }
    .report-panel.is-active { display: block !important; }
.report-panel.report-grid.is-active,
.report-panel[data-report-display="grid"].is-active { display: grid !important; }
    .log-table { border-spacing: 0 .3rem; }
    .log-row td { padding: .65rem .75rem; }
    .communication-section-title { display:flex; align-items:center; gap:.65rem; margin:1.75rem 0 1rem; color:var(--text-dark); font-size:1rem; }
    .communication-section-title i { color:var(--primary-green); }
    .communication-metrics { display:grid; grid-template-columns:repeat(5, minmax(0,1fr)); gap:.75rem; }
    .landing-metrics { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.75rem; }
    .communication-metrics .report-card, .landing-metrics .report-card { padding:1rem; }
    .communication-table-card { background:#fbfcfc; border:1px solid var(--line); border-radius:14px; padding:.25rem .75rem .5rem; }

    @media (max-width: 900px) {
        .report-grid { grid-template-columns: repeat(2, 1fr); }
        .communication-metrics { grid-template-columns:repeat(3,1fr); }
        .landing-metrics { grid-template-columns:repeat(2,1fr); }
    }

    @media (max-width: 480px) {
        .report-grid { grid-template-columns: 1fr; }
        .communication-metrics, .landing-metrics { grid-template-columns:1fr 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">System Reports · {{ $selectedYear }}</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Shipment, delivery, revenue, client, and operational performance for the selected year.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        @php
            $previousYear = $availableYears->filter(fn ($year) => $year < $selectedYear)->first();
            $nextYear = $availableYears->filter(fn ($year) => $year > $selectedYear)->last();
        @endphp
        <div style="display:flex;align-items:center;gap:.4rem;background:#fff;border:1px solid var(--line);border-radius:12px;padding:.25rem;">
            @if($previousYear)<a href="{{ route('admin.reports', ['year' => $previousYear]) }}" style="padding:.55rem .7rem;color:var(--ink);text-decoration:none;" aria-label="Previous year"><i class="fas fa-chevron-left"></i></a>@endif
            <form method="GET" action="{{ route('admin.reports') }}">
                <label for="report-year" style="display:none;">Report year</label>
                <select id="report-year" name="year" onchange="this.form.submit()" style="border:0;background:transparent;color:var(--ink);font-weight:800;padding:.55rem .8rem;cursor:pointer;">
                    @foreach($availableYears as $year)<option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>@endforeach
                </select>
            </form>
            @if($nextYear)<a href="{{ route('admin.reports', ['year' => $nextYear]) }}" style="padding:.55rem .7rem;color:var(--ink);text-decoration:none;" aria-label="Next year"><i class="fas fa-chevron-right"></i></a>@endif
        </div>
        <a href="{{ route('admin.reports.export', ['type' => 'shipments', 'year' => $selectedYear]) }}" style="background: white; color: var(--text-dark); padding: 0.8rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 800; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
            <i class="fas fa-file-csv" style="color: #10b981;"></i> Shipments CSV
        </a>
        <a href="{{ route('admin.reports.export', ['type' => 'clients', 'year' => $selectedYear]) }}" style="background: white; color: var(--text-dark); padding: 0.8rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 800; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
            <i class="fas fa-file-csv" style="color: #6366f1;"></i> Client CRM CSV
        </a>
    </div>
</div>

<nav class="report-nav" aria-label="Report sections">
    <a href="#summary" data-report-tab="summary">Summary</a><a href="#trends" data-report-tab="trends">Trends</a><a href="#operations" data-report-tab="operations">Operations</a><a href="#routes" data-report-tab="routes">Routes</a><a href="#clients" data-report-tab="clients">Clients & quality</a><a href="#communications" data-report-tab="communications">Communications</a><a href="#landing-pages" data-report-tab="landing-pages">Landing pages</a>
</nav>

<div id="summary" class="report-grid report-panel is-active">
    <div class="report-card">
        <div class="report-label"><i class="fas fa-sack-dollar" style="color: #10b981;"></i> Lifetime Revenue</div>
        <div class="report-value">{{ number_format($totalRevenue, 2) }} <span style="font-size: 0.8rem; opacity: 0.5;">ZMW</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-calendar-check" style="color: #3b82f6;"></i> Monthly Revenue</div>
        <div class="report-value">{{ number_format($monthlyRevenue, 2) }} <span style="font-size: 0.8rem; opacity: 0.5;">ZMW</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-users" style="color: #6366f1;"></i> Total Clients</div>
        <div class="report-value">{{ $totalClients }} <span style="font-size: 0.8rem; opacity: 0.5;">ACCOUNTS</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-user-plus" style="color: #f59e0b;"></i> New Leads</div>
        <div class="report-value">{{ $leadsCount }} <span style="font-size: 0.8rem; opacity: 0.5;">PIPELINE</span></div>
    </div>
</div>

<div id="trends" class="report-panel" data-report-display="grid" style="grid-template-columns: 1fr 1fr; gap: 1.25rem;">
    <div class="chart-container">
        <h3 style="font-weight: 800; margin-bottom: 2rem;">Shipment Distribution</h3>
        <canvas id="shipmentChart" style="max-height: 300px;"></canvas>
    </div>
    <div class="chart-container">
        <h3 style="font-weight: 800; margin-bottom: 2rem;">Communication Channels</h3>
        <canvas id="commChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<div id="communications" class="chart-container report-panel">
    <h3 style="font-weight: 800; margin-bottom: 1.25rem;">Communication performance · {{ $selectedYear }}</h3>
    <h4 class="communication-section-title"><i class="fas fa-comments"></i> Messaging performance</h4>
    <div class="communication-metrics" style="margin-bottom:1.5rem;">
        <div class="report-card"><div class="report-label">Messages sent</div><div class="report-value">{{ $communicationMetrics['sent'] }}</div></div>
        <div class="report-card"><div class="report-label">Responses received</div><div class="report-value">{{ $communicationMetrics['received'] }}</div></div>
        <div class="report-card"><div class="report-label">Response rate</div><div class="report-value">{{ $communicationMetrics['response_rate'] }}%</div></div>
        <div class="report-card"><div class="report-label">Delivered</div><div class="report-value">{{ $communicationMetrics['delivered'] }}</div></div>
        <div class="report-card"><div class="report-label">Failed</div><div class="report-value">{{ $communicationMetrics['failed'] }}</div></div>
    </div>
    <h4 class="communication-section-title"><i class="fas fa-clock-rotate-left"></i> Recent activity</h4>
    <div style="overflow-x: auto;">
    <table class="log-table">
        <tbody>
            @foreach($recentLogs as $log)
                <tr class="log-row">
                    <td style="width: 50px;">
                        @if($log->channel === 'sms')
                            <span class="type-pill" style="background: #fffbeb; color: #f59e0b;">SMS</span>
                        @elseif($log->channel === 'whatsapp')
                            <span class="type-pill" style="background: #f0fdf4; color: #22c55e;">WA</span>
                        @else
                            <span class="type-pill" style="background: #f1f5f9; color: #64748b;">CRM</span>
                        @endif
                    </td>
                    <td style="font-weight: 800;">{{ $log->user?->name ?? 'System' }}</td>
                    <td style="color: #64748b;">{{ ucfirst($log->direction) }} · "{{ Str::limit($log->message, 80) }}"</td>
                    <td style="text-align: right; font-size: 0.75rem; color: #94a3b8;">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<div id="landing-pages" class="chart-container report-panel">
    <h3 style="font-weight:800;margin-bottom:1.25rem;">Landing page performance · {{ $selectedYear }}</h3>
    <div class="landing-metrics" style="margin-bottom:1.5rem;">
        <div class="report-card"><div class="report-label"><i class="fas fa-window-maximize" style="color:#007f7f"></i> Landing pages</div><div class="report-value">{{ $landingPageMetrics['pages'] }}</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-eye" style="color:#007f7f"></i> Page views</div><div class="report-value">{{ number_format($landingPageMetrics['views']) }}</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-user-check" style="color:#ff6200"></i> Form submissions</div><div class="report-value">{{ number_format($landingPageMetrics['submissions']) }}</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-chart-line" style="color:#ff6200"></i> Conversion</div><div class="report-value">{{ $landingPageMetrics['conversion_rate'] }}%</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-person-walking-arrow-right" style="color:#ff6200"></i> Bounce rate</div><div class="report-value">{{ $landingPageMetrics['bounce_rate'] }}%</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-clock" style="color:#007f7f"></i> Average time</div><div class="report-value">{{ gmdate('i:s', $landingPageMetrics['average_time']) }}</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-hand-pointer" style="color:#007f7f"></i> CTA clicks</div><div class="report-value">{{ number_format($landingPageMetrics['cta_clicks']) }}</div></div>
        <div class="report-card"><div class="report-label"><i class="fas fa-arrow-trend-up" style="color:#ff6200"></i> CTA rate</div><div class="report-value">{{ $landingPageMetrics['cta_rate'] }}%</div></div>
    </div>
    <h4 class="communication-section-title"><i class="fas fa-ranking-star"></i> Page engagement</h4>
    <div class="communication-table-card"><div style="overflow-x:auto;"><table class="log-table"><thead><tr><th style="text-align:left;padding:1rem;">Page</th><th style="text-align:right;padding:1rem;">Views</th><th style="text-align:right;padding:1rem;">Submissions</th><th style="text-align:right;padding:1rem;">Conversion</th><th style="text-align:left;padding:1rem;">Status</th></tr></thead><tbody>
        @forelse($landingPageReport as $landingPage)
            <tr class="log-row"><td style="font-weight:800;">{{ $landingPage->title }}</td><td style="text-align:right;">{{ number_format($landingPage->views) }}</td><td style="text-align:right;">{{ number_format($landingPage->submissions) }}</td><td style="text-align:right;font-weight:900;color:#007f7f;">{{ $landingPage->report_conversion_rate }}%</td><td><span class="type-pill" style="background:{{ $landingPage->status === 'published' ? '#f0fdf4' : '#f8fafc' }};color:{{ $landingPage->status === 'published' ? '#15803d' : '#64748b' }}">{{ ucfirst($landingPage->status) }}</span></td></tr>
        @empty
            <tr class="log-row"><td colspan="5">No landing pages were created during {{ $selectedYear }}.</td></tr>
        @endforelse
    </tbody></table></div></div>
    <h4 class="communication-section-title"><i class="fas fa-filter-circle-dollar"></i> Traffic sources</h4>
    <div class="communication-table-card"><div style="overflow-x:auto;"><table class="log-table"><thead><tr><th style="text-align:left;padding:1rem;">Source</th><th style="text-align:right;padding:1rem;">Tracked visits</th><th style="text-align:left;padding:1rem;">Next insight</th></tr></thead><tbody>
        @forelse($trafficSources as $traffic)
            <tr class="log-row"><td style="font-weight:800;">{{ ucfirst($traffic->source) }}</td><td style="text-align:right;">{{ number_format($traffic->visits) }}</td><td>Compare engagement and conversion against other sources.</td></tr>
        @empty
            <tr class="log-row"><td colspan="3">Traffic source data will appear as visitors use the landing pages.</td></tr>
        @endforelse
    </tbody></table></div></div>
</div>

<div id="operations" class="chart-container report-panel">
    <h3 style="font-weight:800; margin-bottom:1.25rem;">Shipment operations</h3>
    <div class="report-grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:0;">
        <div class="report-card"><div class="report-label">Delivered</div><div class="report-value">{{ $deliveryMetrics['delivered'] }}</div></div>
        <div class="report-card"><div class="report-label">On time</div><div class="report-value">{{ $deliveryMetrics['on_time'] }}</div></div>
        <div class="report-card"><div class="report-label">Late</div><div class="report-value">{{ $deliveryMetrics['late'] }}</div></div>
        <div class="report-card"><div class="report-label">Average transit</div><div class="report-value">{{ $deliveryMetrics['average_days'] !== null ? number_format($deliveryMetrics['average_days'], 1).' days' : '—' }}</div></div>
    </div>
</div>

<div id="routes" class="report-panel" data-report-display="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;">
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Status breakdown</h3><table class="log-table"><tbody>@foreach($statusBreakdown as $label => $count)<tr class="log-row"><td>{{ $label }}</td><td style="text-align:right;font-weight:900;">{{ $count }}</td></tr>@endforeach</tbody></table></div>
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Delivery quality</h3><table class="log-table"><tbody><tr class="log-row"><td>On time rate</td><td style="text-align:right;font-weight:900;">{{ $deliveryMetrics['on_time_rate'] !== null ? $deliveryMetrics['on_time_rate'].'%' : 'Not enough delivery dates' }}</td></tr><tr class="log-row"><td>On hold / exception</td><td style="text-align:right;font-weight:900;">{{ $exceptionShipments->count() }}</td></tr></tbody></table></div>
</div>

<div class="chart-container report-panel" data-report-group="operations"><h3 style="font-weight:800;margin-bottom:1.25rem;">Monthly shipment and revenue trend</h3><table class="log-table"><thead><tr><th style="text-align:left;padding:1rem;">Month</th><th style="text-align:right;padding:1rem;">Shipments</th><th style="text-align:right;padding:1rem;">Revenue</th></tr></thead><tbody>@foreach($monthlyShipments as $month => $count)<tr class="log-row"><td>{{ $month }}</td><td style="text-align:right;">{{ $count }}</td><td style="text-align:right;">{{ number_format($monthlyRevenueTrend[$month], 2) }} ZMW</td></tr>@endforeach</tbody></table></div>

<div id="clients" class="report-panel" data-report-display="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;">
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Top routes</h3><table class="log-table"><tbody>@forelse($routeBreakdown as $route => $data)<tr class="log-row"><td>{{ $route }}</td><td style="text-align:right;">{{ $data['shipments'] }} · {{ number_format($data['revenue'], 2) }} ZMW</td></tr>@empty<tr><td>No route data.</td></tr>@endforelse</tbody></table></div>
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Shipping methods</h3><table class="log-table"><tbody>@forelse($methodBreakdown as $method => $data)<tr class="log-row"><td>{{ $method }}</td><td style="text-align:right;">{{ $data['shipments'] }} · {{ number_format($data['revenue'], 2) }} ZMW</td></tr>@empty<tr><td>No shipping method data.</td></tr>@endforelse</tbody></table></div>
</div>

<div class="report-panel" data-report-group="clients" data-report-display="grid" style="grid-template-columns:1fr 1fr;gap:2.5rem;">
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Most active clients</h3><table class="log-table"><tbody>@forelse($topClients as $client => $data)<tr class="log-row"><td>{{ $client }}</td><td style="text-align:right;">{{ $data['shipments'] }} · {{ number_format($data['revenue'], 2) }} ZMW</td></tr>@empty<tr><td>No client data.</td></tr>@endforelse</tbody></table></div>
    <div class="chart-container"><h3 style="font-weight:800;margin-bottom:1.25rem;">Data quality</h3><table class="log-table"><tbody><tr class="log-row"><td>Missing tracking number</td><td style="text-align:right;">{{ $dataQuality['missing_tracking'] }}</td></tr><tr class="log-row"><td>Missing ETA</td><td style="text-align:right;">{{ $dataQuality['missing_eta'] }}</td></tr><tr class="log-row"><td>Missing weight</td><td style="text-align:right;">{{ $dataQuality['missing_weight'] }}</td></tr><tr class="log-row"><td>Missing current location</td><td style="text-align:right;">{{ $dataQuality['missing_location'] }}</td></tr></tbody></table></div>
</div>

<div class="chart-container report-panel" data-report-group="operations"><h3 style="font-weight:800;margin-bottom:1.25rem;">Current exceptions and holds</h3><table class="log-table"><thead><tr><th style="text-align:left;padding:1rem;">Shipment</th><th style="text-align:left;padding:1rem;">Client</th><th style="text-align:left;padding:1rem;">Status</th><th style="text-align:left;padding:1rem;">Next action</th></tr></thead><tbody>@forelse($exceptionShipments as $shipment)<tr class="log-row"><td>{{ $shipment->serial_no }}</td><td>{{ $shipment->client_name ?: ($shipment->user->name ?? '—') }}</td><td>{{ $shipment->status_label }}</td><td>{{ $shipment->next_action ?: 'No action recorded' }}</td></tr>@empty<tr><td colspan="4">No current holds or exceptions.</td></tr>@endforelse</tbody></table></div>

<script>
    const reportTabs = document.querySelectorAll('[data-report-tab]');
    const reportPanels = document.querySelectorAll('.report-panel');
    reportTabs.forEach(function (tab) {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            const target = tab.dataset.reportTab;
            reportPanels.forEach(panel => panel.classList.toggle('is-active', panel.id === target || panel.dataset.reportGroup === target));
            reportTabs.forEach(item => item.classList.toggle('is-active', item === tab));
            history.replaceState(null, '', '#' + target);
        });
    });
    const initialReportTab = window.location.hash.replace('#', '');
    const initialTab = Array.from(reportTabs).find(tab => tab.dataset.reportTab === initialReportTab) || reportTabs[0];
    if (initialTab) initialTab.click();
</script>
<script>
    // Shipment Chart
    const shipCtx = document.getElementById('shipmentChart').getContext('2d');
    new Chart(shipCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($shipmentStats->keys()) !!},
            datasets: [{
                data: {!! json_encode($shipmentStats->values()) !!},
                backgroundColor: ['#4caf50', '#3b82f6', '#f59e0b', '#6366f1', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Comm Chart
    const commCtx = document.getElementById('commChart').getContext('2d');
    new Chart(commCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($commStats->keys()) !!},
            datasets: [{
                label: 'Messages Sent',
                data: {!! json_encode($commStats->values()) !!},
                backgroundColor: '#6366f1',
                borderRadius: 10
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
