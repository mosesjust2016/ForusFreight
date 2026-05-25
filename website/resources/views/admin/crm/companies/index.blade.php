@extends('layouts.dashboard')

@section('title', 'Companies - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .company-row { transition: all 0.2s; }
    .company-row:hover { background: #fcfdfe; }
    .company-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .company-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .company-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .stat-pill { display: inline-block; padding: 0.35rem 0.7rem; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 0.8rem; font-weight: 700; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Companies</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Manage corporate accounts and their linked contacts.</p>
        </div>
        <a href="{{ route('admin.crm.companies.create') }}" style="background: var(--primary-green); color: white; padding: 0.85rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-decoration: none;">
            <i class="fas fa-plus"></i> Add Company
        </a>
    </div>
</div>

<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Company</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Industry</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Contacts</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Pipeline</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
            <tr class="company-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $company->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-gray); font-weight: 600;">{{ $company->city ?? 'No city' }}, {{ $company->country }}</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.9rem; color: #475569; font-weight: 600;">{{ $company->industry ?? 'N/A' }}</td>
                <td style="padding: 1.25rem 1rem;"><span class="stat-pill"><i class="fas fa-users" style="margin-right: 0.4rem;"></i> {{ $company->contacts_count }} Linked</span></td>
                <td style="padding: 1.25rem 1rem;"><span class="stat-pill"><i class="fas fa-handshake" style="margin-right: 0.4rem;"></i> {{ $company->deals_count }} Deals</span></td>
                <td style="padding: 1.25rem 1rem;">
                    <span class="status-badge" style="background: {{ $company->status === 'active' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $company->status === 'active' ? '#16a34a' : '#ef4444' }};">{{ strtoupper($company->status) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <a href="{{ route('admin.crm.companies.show', $company) }}" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.crm.companies.edit', $company) }}" class="action-btn" title="Edit"><i class="fas fa-pen"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-building" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Companies Yet</p>
                    <p style="font-size: 0.85rem;">Start by adding your first corporate account.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
