@extends('layouts.dashboard')

@section('title', $deal->title . ' - Deal - Forus Freight')

@section('styles')
<style>
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .detail-item { margin-bottom: 1.25rem; }
    .detail-label { font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem; }
    .detail-value { font-size: 1rem; font-weight: 700; color: var(--text-dark); }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.pipeline') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Pipeline
    </a>
</div>

<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark);">{{ $deal->title }}</h1>
            <span style="padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; background: {{ $deal->stage->color }}20; color: {{ $deal->stage->color }};">{{ $deal->stage->name }}</span>
            <span class="status-badge" style="background: {{ $deal->priority === 'high' ? '#fef2f2' : ($deal->priority === 'medium' ? '#fffbeb' : '#f0fdf4') }}; color: {{ $deal->priority === 'high' ? '#ef4444' : ($deal->priority === 'medium' ? '#f59e0b' : '#16a34a') }};">{{ strtoupper($deal->priority) }}</span>
        </div>
        <div style="display: flex; gap: 1.5rem; color: var(--text-gray); font-size: 0.9rem;">
            <span><i class="fas fa-user"></i> {{ $deal->contact?->name ?? 'No contact' }}</span>
            <span><i class="fas fa-building"></i> {{ $deal->company?->name ?? 'No company' }}</span>
            <span><i class="fas fa-user-tie"></i> {{ $deal->assignedTo?->name ?? 'Unassigned' }}</span>
        </div>
    </div>
    <div style="text-align: right;">
        <div style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark);">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</div>
        <div style="font-size: 0.8rem; color: var(--text-gray); font-weight: 700;">Expected: {{ $deal->expected_close_date?->format('M d, Y') ?? 'TBD' }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-list-check" style="color: var(--primary-green);"></i> Tasks ({{ $deal->tasks->count() }})</h2>
            @forelse($deal->tasks as $task)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $task->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $task->type }} &middot; Due {{ $task->due_at?->diffForHumans() ?? 'No due date' }}</div>
                </div>
                <span style="padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; background: {{ $task->status === 'completed' ? '#f0fdf4' : '#fffbeb' }}; color: {{ $task->status === 'completed' ? '#16a34a' : '#b45309' }};">{{ strtoupper($task->status) }}</span>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No tasks for this deal.</p>
            @endforelse
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-file-lines" style="color: #3b82f6;"></i> Documents ({{ $deal->documents->count() }})</h2>
            @forelse($deal->documents as $doc)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $doc->title }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ ucfirst($doc->type) }} &middot; {{ $doc->status }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 0.85rem;">{{ $doc->amount ? number_format($doc->amount, 2) . ' ' . $doc->currency : 'N/A' }}</div>
                </div>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No documents attached.</p>
            @endforelse
        </div>
    </div>

    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-circle-info" style="color: #f59e0b;"></i> Deal Details</h2>
            <div class="detail-item">
                <div class="detail-label">Source</div>
                <div class="detail-value">{{ $deal->source ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created</div>
                <div class="detail-value">{{ $deal->created_at->format('M d, Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Expected Close</div>
                <div class="detail-value">{{ $deal->expected_close_date?->format('M d, Y') ?? 'TBD' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Actual Close</div>
                <div class="detail-value">{{ $deal->actual_close_date?->format('M d, Y') ?? 'Open' }}</div>
            </div>
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-align-left" style="color: #8b5cf6;"></i> Description</h2>
            <p style="color: #475569; font-weight: 600; line-height: 1.6;">{{ $deal->description ?? 'No description provided.' }}</p>
        </div>
    </div>
</div>
@endsection
