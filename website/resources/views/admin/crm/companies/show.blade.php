@extends('layouts.dashboard')

@section('title', $company->name . ' - Company - Forus Freight')

@section('styles')
<style>
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); height: 100%; }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .detail-item { margin-bottom: 1.25rem; }
    .detail-label { font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem; }
    .detail-value { font-size: 1rem; font-weight: 700; color: var(--text-dark); }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .contact-chip { display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 0.5rem; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.companies') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Companies
    </a>
</div>

<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">{{ $company->name }}</h1>
        <div style="display: flex; gap: 1.5rem; color: var(--text-gray); font-size: 0.95rem; align-items: center;">
            <span><i class="fas fa-building"></i> {{ $company->industry ?? 'No industry' }}</span>
            <span><i class="fas fa-location-dot"></i> {{ $company->city ?? 'No city' }}, {{ $company->country }}</span>
            <span class="status-badge" style="background: {{ $company->status === 'active' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $company->status === 'active' ? '#16a34a' : '#ef4444' }};">{{ $company->status }}</span>
        </div>
    </div>
    <a href="{{ route('admin.crm.companies.edit', $company) }}" style="background: var(--primary-green); color: white; padding: 0.85rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 800;">
        <i class="fas fa-pen"></i> Edit
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-users" style="color: var(--primary-green);"></i> Linked Contacts ({{ $company->contacts->count() }})</h2>
            @forelse($company->contacts as $contact)
            <div class="contact-chip">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-green-light); color: var(--primary-green); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.9rem;">{{ substr($contact->name, 0, 1) }}</div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $contact->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $contact->email }} &middot; {{ $contact->pivot->role }}</div>
                </div>
                @if($contact->pivot->is_primary)
                    <span class="status-badge" style="background: #eff6ff; color: #1d4ed8;">PRIMARY</span>
                @endif
                <a href="{{ route('admin.crm.contacts.360', $contact) }}" class="action-btn" title="360 View"><i class="fas fa-eye"></i></a>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No contacts linked yet.</p>
            @endforelse
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-handshake" style="color: #8b5cf6;"></i> Deals ({{ $company->deals->count() }})</h2>
            @forelse($company->deals as $deal)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-dark);">{{ $deal->title }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-gray); font-weight: 600;">{{ $deal->stage->name }} &middot; {{ $deal->contact?->name ?? 'No contact' }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 900; color: var(--text-dark);">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-gray);">Expected: {{ $deal->expected_close_date?->format('M d, Y') ?? 'TBD' }}</div>
                </div>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No deals yet.</p>
            @endforelse
        </div>
    </div>

    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-address-card" style="color: #3b82f6;"></i> Company Info</h2>
            <div class="detail-item">
                <div class="detail-label">Website</div>
                <div class="detail-value">{{ $company->website ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $company->email ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">{{ $company->phone ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tax ID</div>
                <div class="detail-value">{{ $company->tax_id ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Annual Revenue</div>
                <div class="detail-value">{{ $company->annual_revenue ? number_format($company->annual_revenue, 2) . ' ZMW' : 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Employees</div>
                <div class="detail-value">{{ $company->employee_count ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-user-tie" style="color: #f59e0b;"></i> Assigned Agent</h2>
            @if($company->assignedAgent)
            <div class="contact-chip">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.8rem;">{{ substr($company->assignedAgent->name, 0, 1) }}</div>
                <div>
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $company->assignedAgent->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $company->assignedAgent->email }}</div>
                </div>
            </div>
            @else
            <p style="color: var(--text-gray); text-align: center; padding: 1rem 0;">No agent assigned.</p>
            @endif
        </div>
    </div>
</div>
@endsection
