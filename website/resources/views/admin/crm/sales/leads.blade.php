@extends('layouts.dashboard')

@section('title', 'Leads - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .lead-row { transition: all 0.2s; }
    .lead-row:hover { background: #fcfdfe; }
    .lead-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .lead-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .lead-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .stat-pill { display: inline-block; padding: 0.35rem 0.7rem; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 0.8rem; font-weight: 700; }
    .score-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Lead Routing</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Score, route, and convert leads into active clients.</p>
        </div>
    </div>
</div>

<!-- Leads List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Lead</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Email / Phone</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Lead Score</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Shipments</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Assigned Agent</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
            <tr class="lead-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $lead->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $lead->company_name ?? 'No company' }}</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">
                    <div>{{ $lead->email }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray);">{{ $lead->phone ?? 'No phone' }}</div>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    @php
                        $scoreColor = $lead->lead_score >= 70 ? '#f0fdf4' : ($lead->lead_score >= 40 ? '#fff8e1' : '#fef2f2');
                        $scoreText = $lead->lead_score >= 70 ? '#16a34a' : ($lead->lead_score >= 40 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <span class="score-badge" style="background: {{ $scoreColor }}; color: {{ $scoreText }};">{{ $lead->lead_score }}/100</span>
                </td>
                <td style="padding: 1.25rem 1rem;"><span class="stat-pill">{{ $lead->shipments_count }} Orders</span></td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $lead->assigned_agent ?? 'Unassigned' }}</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    <form action="{{ route('admin.crm.leads.assign', $lead) }}" method="POST" class="form-inline" style="justify-content: flex-end;">
                        @csrf
                        @method('PUT')
                        <select name="assigned_agent" required onchange="this.form.submit()">
                            <option value="">Assign &rarr;</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.crm.contacts.360', $lead) }}" class="action-btn" title="360 View"><i class="fas fa-eye"></i></a>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-user-clock" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Leads Found</p>
                    <p style="font-size: 0.85rem;">All contacts are active or there are no registered leads.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $leads->links() }}</div>
</div>
@endsection
