@extends('layouts.dashboard')

@section('title', 'Ticket #' . $ticket->ticket_number . ' - Forus Freight')

@section('styles')
<style>
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .priority-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .reply-bubble { padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 1rem; }
    .reply-internal { background: #fef2f2; border: 1px solid #fee2e2; }
    .reply-public { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.tickets') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Tickets
    </a>
</div>

<!-- Ticket Header -->
<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
            <h1 style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">#{{ $ticket->ticket_number }}</h1>
            @php $statusColors = ['open' => ['#fef2f2','#ef4444'], 'in_progress' => ['#fff8e1','#f59e0b'], 'waiting' => ['#e3f2fd','#1e88e5'], 'resolved' => ['#f0fdf4','#16a34a'], 'closed' => ['#f1f5f9','#475569']]; @endphp
            <span class="status-badge" style="background: {{ $statusColors[$ticket->status][0] }}; color: {{ $statusColors[$ticket->status][1] }};">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
            @php $priorityColors = ['low' => ['#f0fdf4','#16a34a'], 'medium' => ['#fff8e1','#f59e0b'], 'high' => ['#fef2f2','#ef4444'], 'urgent' => ['#fef2f2','#dc2626']]; @endphp
            <span class="priority-badge" style="background: {{ $priorityColors[$ticket->priority][0] }}; color: {{ $priorityColors[$ticket->priority][1] }};">{{ ucfirst($ticket->priority) }}</span>
        </div>
        <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem;">{{ $ticket->subject }}</h2>
        <div style="color: var(--text-gray); font-size: 0.9rem; display: flex; gap: 1.5rem; align-items: center;">
            <span><i class="fas fa-user"></i> {{ $ticket->contact?->name ?? 'No contact' }}</span>
            <span><i class="fas fa-building"></i> {{ $ticket->company?->name ?? 'No company' }}</span>
            <span><i class="fas fa-comments"></i> {{ ucfirst($ticket->channel) }}</span>
            <span><i class="fas fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}</span>
        </div>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <form action="{{ route('admin.crm.tickets.status', $ticket) }}" method="POST" class="form-inline">
            @csrf
            @method('PUT')
            <select name="status" onchange="this.form.submit()">
                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting" {{ $ticket->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </form>
        <form action="{{ route('admin.crm.tickets.assign', $ticket) }}" method="POST" class="form-inline">
            @csrf
            @method('PUT')
            <select name="assigned_to" onchange="this.form.submit()">
                <option value="">Assign</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <!-- Replies -->
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-comments" style="color: #3b82f6;"></i> Conversation</h2>
            <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #f8fafc; border-radius: 12px;">
                <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Original Description</div>
                <div style="font-size: 0.95rem; color: #475569; font-weight: 600; line-height: 1.6;">{{ $ticket->description }}</div>
            </div>

            @forelse($ticket->replies as $reply)
            <div class="reply-bubble {{ $reply->is_internal ? 'reply-internal' : 'reply-public' }}">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">
                        {{ $reply->user->name }}
                        @if($reply->is_internal)
                            <span style="font-size: 0.65rem; color: #ef4444; font-weight: 800; margin-left: 0.5rem;">INTERNAL</span>
                        @endif
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">{{ $reply->created_at->diffForHumans() }}</div>
                </div>
                <div style="font-size: 0.9rem; color: #475569; font-weight: 600; line-height: 1.5;">{{ $reply->message }}</div>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 1rem 0;">No replies yet.</p>
            @endforelse

            <!-- Reply Form -->
            <form action="{{ route('admin.crm.tickets.reply', $ticket) }}" method="POST" style="margin-top: 1.5rem;">
                @csrf
                <textarea name="message" rows="3" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.9rem; outline: none; resize: none; margin-bottom: 0.75rem;" placeholder="Type your reply..."></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600; color: #475569;">
                        <input type="checkbox" name="is_internal" value="1"> Internal Note
                    </label>
                    <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Send Reply</button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-info-circle" style="color: #8b5cf6;"></i> Ticket Details</h2>
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem;">Category</div>
                <div style="font-size: 1rem; font-weight: 700; color: var(--text-dark);">{{ $ticket->category ?? 'Uncategorized' }}</div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem;">Assigned To</div>
                <div style="font-size: 1rem; font-weight: 700; color: var(--text-dark);">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem;">Created</div>
                <div style="font-size: 1rem; font-weight: 700; color: var(--text-dark);">{{ $ticket->created_at->format('M d, Y H:i') }}</div>
            </div>
            @if($ticket->resolved_at)
            <div>
                <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem;">Resolved</div>
                <div style="font-size: 1rem; font-weight: 700; color: var(--primary-green);">{{ $ticket->resolved_at->format('M d, Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
