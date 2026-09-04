@extends('layouts.dashboard')

@section('title', 'Campaigns - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .campaign-row { transition: all 0.2s; }
    .campaign-row:hover { background: #fcfdfe; }
    .campaign-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .campaign-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .campaign-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.9rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Marketing Campaigns</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Track email, social, and digital ad campaigns with ROI and lead scoring.</p>
        </div>
    </div>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Active Campaigns</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ $stats['active'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Total Budget</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ number_format($stats['total_budget'], 2) }} ZMW</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Leads Generated</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">{{ $stats['total_leads'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Conversions</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #8e24aa;">{{ $stats['total_conversions'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.campaigns') }}" class="{{ !request('status') && !request('type') ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.crm.campaigns', ['status' => 'active']) }}" class="{{ request('status') === 'active' ? 'active' : '' }}">Active</a>
    <a href="{{ route('admin.crm.campaigns', ['status' => 'draft']) }}" class="{{ request('status') === 'draft' ? 'active' : '' }}">Draft</a>
    <a href="{{ route('admin.crm.campaigns', ['status' => 'completed']) }}" class="{{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
    <a href="{{ route('admin.crm.campaigns', ['type' => 'email']) }}" class="{{ request('type') === 'email' ? 'active' : '' }}">Email</a>
    <a href="{{ route('admin.crm.campaigns', ['type' => 'social']) }}" class="{{ request('type') === 'social' ? 'active' : '' }}">Social</a>
    <a href="{{ route('admin.crm.campaigns', ['type' => 'digital_ad']) }}" class="{{ request('type') === 'digital_ad' ? 'active' : '' }}">Digital Ads</a>
</div>

<!-- Quick Create -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Create Campaign</h2>
    <form action="{{ route('admin.crm.campaigns.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="name" placeholder="Campaign name" required>
        <select name="type" required>
            <option value="email">Email</option>
            <option value="social">Social Media</option>
            <option value="digital_ad">Digital Ad</option>
            <option value="mixed">Mixed</option>
        </select>
        <select name="status" required>
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="completed">Completed</option>
        </select>
        <input type="date" name="start_date">
        <input type="date" name="end_date">
        <input type="number" name="budget" placeholder="Budget" step="0.01">
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create</button>
    </form>
</div>

<!-- Campaigns List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Campaign</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Type</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Budget</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Leads</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Conversions</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($campaigns as $campaign)
            <tr class="campaign-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $campaign->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $campaign->start_date?->format('M d') ?? 'TBD' }} - {{ $campaign->end_date?->format('M d') ?? 'TBD' }}</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ ucfirst(str_replace('_', ' ', $campaign->type)) }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @php $statusColors = ['draft' => ['#f1f5f9','#475569'], 'active' => ['#f0fdf4','#16a34a'], 'paused' => ['#fff8e1','#f59e0b'], 'completed' => ['#e3f2fd','#1e88e5']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$campaign->status][0] }}; color: {{ $statusColors[$campaign->status][1] }};">{{ ucfirst($campaign->status) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ number_format($campaign->budget ?? 0, 2) }} ZMW</td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ $campaign->leads_generated }}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ $campaign->conversions }}</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    <form action="{{ route('admin.crm.campaigns.update', $campaign) }}" method="POST" class="form-inline" style="justify-content: flex-end;">
                        @csrf
                        @method('PUT')
                        <input type="number" name="spent" placeholder="Spent" value="{{ $campaign->spent }}" step="0.01" style="width: 100px;">
                        <input type="number" name="leads_generated" placeholder="Leads" value="{{ $campaign->leads_generated }}" style="width: 80px;">
                        <input type="number" name="conversions" placeholder="Conv." value="{{ $campaign->conversions }}" style="width: 80px;">
                        <button type="submit" class="action-btn" title="Update"><i class="fas fa-save"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-bullhorn" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Campaigns Found</p>
                    <p style="font-size: 0.85rem;">Create your first marketing campaign.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $campaigns->links() }}</div>
</div>
@endsection
