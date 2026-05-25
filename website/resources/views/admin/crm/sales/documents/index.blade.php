@extends('layouts.dashboard')

@section('title', 'Documents - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .doc-row { transition: all 0.2s; }
    .doc-row:hover { background: #fcfdfe; }
    .doc-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .doc-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .doc-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .stat-pill { display: inline-block; padding: 0.35rem 0.7rem; background: #f1f5f9; color: #475569; border-radius: 8px; font-size: 0.8rem; font-weight: 700; }
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
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Quotes & Proposals</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Manage quotes, proposals, and contracts linked to deals and contacts.</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.documents') }}" class="{{ !request('type') ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.crm.documents', ['type' => 'quote']) }}" class="{{ request('type') === 'quote' ? 'active' : '' }}">Quotes</a>
    <a href="{{ route('admin.crm.documents', ['type' => 'proposal']) }}" class="{{ request('type') === 'proposal' ? 'active' : '' }}">Proposals</a>
    <a href="{{ route('admin.crm.documents', ['type' => 'contract']) }}" class="{{ request('type') === 'contract' ? 'active' : '' }}">Contracts</a>
</div>

<!-- Quick Create -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Quick Create Document</h2>
    <form action="{{ route('admin.crm.documents.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="title" placeholder="Document title" required>
        <select name="type" required>
            <option value="quote">Quote</option>
            <option value="proposal">Proposal</option>
            <option value="contract">Contract</option>
            <option value="invoice">Invoice</option>
        </select>
        <input type="number" name="amount" placeholder="Amount" step="0.01">
        <input type="text" name="currency" placeholder="Currency" value="ZMW">
        <input type="datetime-local" name="expires_at" placeholder="Expires at">
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create</button>
    </form>
</div>

<!-- Documents List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Document</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Type</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Amount</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Contact / Deal</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Created</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr class="doc-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $doc->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">by {{ $doc->creator->name }}</div>
                </td>
                <td style="padding: 1.25rem 1rem;"><span class="stat-pill">{{ ucfirst($doc->type) }}</span></td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ $doc->amount ? number_format($doc->amount, 2) . ' ' . $doc->currency : 'N/A' }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @php $statusColors = ['draft' => ['#f1f5f9','#475569'], 'sent' => ['#fff8e1','#f59e0b'], 'accepted' => ['#f0fdf4','#16a34a'], 'rejected' => ['#fef2f2','#ef4444'], 'expired' => ['#f3f4f6','#9ca3af']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$doc->status][0] }}; color: {{ $statusColors[$doc->status][1] }};">{{ ucfirst($doc->status) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">
                    {{ $doc->contact?->name ?? ($doc->deal?->title ?? 'N/A') }}
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $doc->created_at->format('M d, Y') }}</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    @if($doc->status === 'draft')
                    <form action="{{ route('admin.crm.documents.send', $doc) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn" title="Send" style="width: auto; padding: 0 0.75rem; font-weight: 800; color: var(--primary-green);"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Documents Found</p>
                    <p style="font-size: 0.85rem;">Create your first quote or proposal.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $documents->links() }}</div>
</div>
@endsection
