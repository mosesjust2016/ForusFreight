@extends('layouts.dashboard')

@section('title', 'Support Tickets - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .ticket-row { transition: all 0.2s; }
    .ticket-row:hover { background: #fcfdfe; }
    .ticket-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .ticket-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .ticket-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .priority-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.9rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select, .form-inline textarea { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Support Tickets</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Omni-channel support across email, chat, phone, and web.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.crm.tickets', ['mine' => 1]) }}" class="btn-primary" style="background: #f1f5f9; color: #475569;"><i class="fas fa-user-check"></i> My Tickets</a>
        </div>
    </div>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Open</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444;">{{ $stats['open'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">In Progress</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #f59e0b;">{{ $stats['in_progress'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Resolved</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ $stats['resolved'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Urgent Open</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #dc2626;">{{ $stats['urgent'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.tickets') }}" class="{{ !request('status') && !request('priority') ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.crm.tickets', ['status' => 'open']) }}" class="{{ request('status') === 'open' ? 'active' : '' }}">Open</a>
    <a href="{{ route('admin.crm.tickets', ['status' => 'in_progress']) }}" class="{{ request('status') === 'in_progress' ? 'active' : '' }}">In Progress</a>
    <a href="{{ route('admin.crm.tickets', ['status' => 'resolved']) }}" class="{{ request('status') === 'resolved' ? 'active' : '' }}">Resolved</a>
    <a href="{{ route('admin.crm.tickets', ['priority' => 'urgent']) }}" class="{{ request('priority') === 'urgent' ? 'active' : '' }}">Urgent</a>
</div>

<!-- Quick Create -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Create Ticket</h2>
    <form action="{{ route('admin.crm.tickets.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="subject" placeholder="Subject" required>
        <select name="channel" required>
            <option value="email">Email</option>
            <option value="chat">Chat</option>
            <option value="phone">Phone</option>
            <option value="web">Web</option>
        </select>
        <select name="priority" required>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>
        <select name="assigned_to">
            <option value="">Unassigned</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
            @endforeach
        </select>
        <textarea name="description" placeholder="Description" rows="1" style="resize: vertical;"></textarea>
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create</button>
    </form>
</div>

<!-- Tickets List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Ticket</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Channel</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Priority</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Contact</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Assigned</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr class="ticket-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">#{{ $ticket->ticket_number }}</div>
                    <div style="font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $ticket->subject }}</div>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700; text-transform: capitalize;">{{ $ticket->channel }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @php $priorityColors = ['low' => ['#f0fdf4','#16a34a'], 'medium' => ['#fff8e1','#f59e0b'], 'high' => ['#fef2f2','#ef4444'], 'urgent' => ['#fef2f2','#dc2626']]; @endphp
                    <span class="priority-badge" style="background: {{ $priorityColors[$ticket->priority][0] }}; color: {{ $priorityColors[$ticket->priority][1] }};">{{ ucfirst($ticket->priority) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    @php $statusColors = ['open' => ['#fef2f2','#ef4444'], 'in_progress' => ['#fff8e1','#f59e0b'], 'waiting' => ['#e3f2fd','#1e88e5'], 'resolved' => ['#f0fdf4','#16a34a'], 'closed' => ['#f1f5f9','#475569']]; @endphp
                    <span class="status-badge" style="background: {{ $statusColors[$ticket->status][0] }}; color: {{ $statusColors[$ticket->status][1] }};">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                </td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $ticket->contact?->name ?? 'N/A' }}</td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    <a href="{{ route('admin.crm.tickets.show', $ticket) }}" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-headset" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Tickets Found</p>
                    <p style="font-size: 0.85rem;">All caught up! Create a ticket if needed.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $tickets->links() }}</div>
</div>
@endsection
