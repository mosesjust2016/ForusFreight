@extends('layouts.dashboard')

@section('title', 'Sales Pipeline - CRM - Forus Freight')

@section('styles')
<style>
    .pipeline-header { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .stage-card { background: white; border-radius: 16px; padding: 1.25rem; min-width: 220px; box-shadow: var(--shadow); flex-shrink: 0; border-top: 4px solid var(--stage-color, #4caf50); }
    .stage-name { font-size: 0.85rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
    .stage-meta { font-size: 0.75rem; color: var(--text-gray); font-weight: 700; }
    .deal-card { background: white; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1.5px solid #f1f5f9; cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; color: inherit; }
    .deal-card:hover { border-color: var(--primary-green); transform: translateY(-2px); }
    .deal-title { font-weight: 800; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.25rem; }
    .deal-value { font-size: 0.85rem; font-weight: 700; color: var(--primary-green); }
    .deal-meta { font-size: 0.7rem; color: var(--text-gray); margin-top: 0.5rem; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar input { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Sales Pipeline</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Track deals through customizable stages and monitor pipeline value.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.crm.stages') }}" style="padding: 0.75rem 1.25rem; border-radius: 12px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none;"><i class="fas fa-layer-group"></i> Stages</a>
            <a href="{{ route('admin.crm.deals.create') }}" style="background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-decoration: none;"><i class="fas fa-plus"></i> New Deal</a>
        </div>
    </div>
</div>

<!-- Pipeline Stats -->
<div class="pipeline-header">
    @foreach($pipelineStats as $stat)
    <div class="stage-card" style="--stage-color: {{ $stat['color'] }};">
        <div class="stage-name">{{ $stat['name'] }}</div>
        <div class="stage-meta">{{ $stat['count'] }} Deals &middot; {{ number_format($stat['value'], 2) }} ZMW</div>
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.crm.pipeline') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <select name="stage" onchange="this.form.submit()">
            <option value="">All Stages</option>
            @foreach($stages as $stage)
                <option value="{{ $stage->id }}" {{ request('stage') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
            @endforeach
        </select>
        <select name="agent" onchange="this.form.submit()">
            <option value="">All Agents</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
            @endforeach
        </select>
        <select name="priority" onchange="this.form.submit()">
            <option value="">All Priorities</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
        </select>
        <a href="{{ route('admin.crm.pipeline') }}" style="padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.9rem;">Reset</a>
    </form>
</div>

<!-- Deals List -->
<div style="background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow);">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Deal</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Contact / Company</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Value</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Stage</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Expected Close</th>
                <th style="padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Agent</th>
                <th style="text-align: right; padding: 1rem 0; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deals as $deal)
            <tr style="border-bottom: 1px solid #f8fafc; transition: background 0.2s;">
                <td style="padding: 1.25rem 0;">
                    <div style="font-weight: 800; color: var(--text-dark); font-size: 0.95rem;">{{ $deal->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $deal->source ?? 'No source' }}</div>
                </td>
                <td style="padding: 1.25rem 0;">
                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-dark);">{{ $deal->contact?->name ?? 'No contact' }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $deal->company?->name ?? 'No company' }}</div>
                </td>
                <td style="padding: 1.25rem 0;">
                    <div style="font-weight: 900; color: var(--text-dark);">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</div>
                </td>
                <td style="padding: 1.25rem 0;">
                    <span style="padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800; background: {{ $deal->stage->color }}20; color: {{ $deal->stage->color }};">{{ $deal->stage->name }}</span>
                </td>
                <td style="padding: 1.25rem 0; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $deal->expected_close_date?->format('M d, Y') ?? 'TBD' }}</td>
                <td style="padding: 1.25rem 0; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $deal->assignedTo?->name ?? 'Unassigned' }}</td>
                <td style="padding: 1.25rem 0; text-align: right;">
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                        <a href="{{ route('admin.crm.deals.show', $deal) }}" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.crm.deals.show', $deal) }}" class="action-btn" title="Edit" style="margin-right: 0.25rem;"><i class="fas fa-pen"></i></a>
                        <form action="{{ route('admin.crm.deals.stage', $deal) }}" method="POST" style="display: flex; gap: 0.25rem;">
                            @csrf
                            @method('PUT')
                            <select name="deal_stage_id" onchange="this.form.submit()" style="padding: 0.35rem 0.6rem; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.75rem; font-weight: 700; cursor: pointer; outline: none;">
                                <option value="" disabled selected>Move &rarr;</option>
                                @foreach($stages as $stage)
                                    @if($deal->deal_stage_id != $stage->id)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-funnel-dollar" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Deals Found</p>
                    <p style="font-size: 0.85rem;">Create your first deal to start tracking the pipeline.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $deals->links() }}</div>
</div>
@endsection
