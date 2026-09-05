@extends('layouts.dashboard')

@section('title', 'Sales Forecast - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); overflow-x: auto; }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .forecast-row td { padding: 1.25rem 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .progress-bar { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--primary-green); border-radius: 10px; }
    .agent-row { transition: all 0.2s; }
    .agent-row:hover { background: #fcfdfe; }
    .agent-row td { padding: 1.25rem 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(3, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 480px) {
        div[style*="grid-template-columns: repeat(3, 1fr)"] { grid-template-columns: 1fr !important; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Sales Forecast</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Weighted pipeline forecast, revenue tracking, and agent performance.</p>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Won Revenue (YTD)</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ usd($wonRevenue) }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Open Pipeline</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ usd($pipelineTotal) }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Forecast Accuracy</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">~{{ $pipelineTotal > 0 ? round(($wonRevenue / ($wonRevenue + $pipelineTotal)) * 100, 1) : 0 }}%</div>
    </div>
</div>

<!-- 3-Month Forecast -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">3-Month Outlook</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Month</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Deals</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Total Value</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Weighted Forecast</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Confidence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($next3Months as $month)
            <tr class="forecast-row">
                <td style="font-weight: 800; color: var(--text-dark);">{{ $month['month'] }}</td>
                <td style="font-weight: 700; color: #475569;">{{ $month['deal_count'] }}</td>
                <td style="font-weight: 700; color: #475569;">{{ usd($month['total_value']) }}</td>
                <td style="font-weight: 900; color: var(--primary-green);">{{ usd($month['weighted_forecast']) }}</td>
                <td style="width: 150px;">
                    @php $confidence = $month['total_value'] > 0 ? round(($month['weighted_forecast'] / $month['total_value']) * 100) : 0; @endphp
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div class="progress-bar" style="flex-grow: 1;">
                            <div class="progress-fill" style="width: {{ $confidence }}%;"></div>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 800; color: #475569;">{{ $confidence }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Agent Performance -->
<div class="crm-grid">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Agent Performance</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Agent</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Deals</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Pipeline Value</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Quota Tracking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agentPerformance as $agent)
            <tr class="agent-row">
                <td style="font-weight: 800; color: var(--text-dark);">{{ $agent->name }}</td>
                <td style="font-weight: 700; color: #475569;">{{ $agent->assigned_deals_count ?? 0 }}</td>
                <td style="font-weight: 900; color: var(--text-dark);">{{ usd($agent->total_pipeline ?? 0) }}</td>
                <td style="width: 200px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div class="progress-bar" style="flex-grow: 1;">
                            <div class="progress-fill" style="width: {{ min(100, ($agent->total_pipeline ?? 0) / 100000 * 100) }}%;"></div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <p style="font-weight: 800;">No Agent Data</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
