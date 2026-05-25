@extends('layouts.dashboard')

@section('title', 'Tasks - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .task-row { transition: all 0.2s; }
    .task-row:hover { background: #fcfdfe; }
    .task-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .task-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .task-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
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
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Tasks</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Manage follow-ups, calls, meetings, and deal-related actions.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.crm.tasks', ['mine' => 1]) }}" class="btn-primary" style="background: #f1f5f9; color: #475569;"><i class="fas fa-user-check"></i> My Tasks</a>
        </div>
    </div>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Total</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $stats['total'] }}</div>
    </div>
    <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Pending</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #f59e0b;">{{ $stats['pending'] }}</div>
    </div>
    <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Overdue</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444;">{{ $stats['overdue'] }}</div>
    </div>
    <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Completed</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ $stats['completed'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.tasks') }}" class="{{ !request('status') && !request('type') ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.crm.tasks', ['status' => 'pending']) }}" class="{{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.crm.tasks', ['status' => 'completed']) }}" class="{{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
    <a href="{{ route('admin.crm.tasks', ['type' => 'follow_up']) }}" class="{{ request('type') === 'follow_up' ? 'active' : '' }}">Follow-ups</a>
    <a href="{{ route('admin.crm.tasks', ['type' => 'call']) }}" class="{{ request('type') === 'call' ? 'active' : '' }}">Calls</a>
    <a href="{{ route('admin.crm.tasks', ['type' => 'meeting']) }}" class="{{ request('type') === 'meeting' ? 'active' : '' }}">Meetings</a>
</div>

<!-- Add Task -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Quick Create Task</h2>
    <form action="{{ route('admin.crm.tasks.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="title" placeholder="Task title" required>
        <select name="type" required>
            <option value="follow_up">Follow-up</option>
            <option value="call">Call</option>
            <option value="meeting">Meeting</option>
            <option value="email">Email</option>
            <option value="proposal">Proposal</option>
            <option value="quote">Quote</option>
        </select>
        <select name="assigned_to">
            <option value="">Unassigned</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
            @endforeach
        </select>
        <select name="contact_id">
            <option value="">No Contact</option>
            @foreach($contacts as $contact)
                <option value="{{ $contact->id }}">{{ $contact->name }}</option>
            @endforeach
        </select>
        <select name="deal_id">
            <option value="">No Deal</option>
            @foreach($deals as $deal)
                <option value="{{ $deal->id }}">{{ $deal->title }}</option>
            @endforeach
        </select>
        <input type="datetime-local" name="due_at">
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create</button>
    </form>
</div>

<!-- Tasks List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Task</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Type</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Assigned</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Due</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr class="task-row">
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: var(--text-dark);">{{ $task->title }}</div>
                    @if($task->deal)
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;"><i class="fas fa-handshake"></i> {{ $task->deal->title }}</div>
                    @endif
                </td>
                <td style="padding: 1.25rem 1rem;"><span class="stat-pill">{{ ucfirst(str_replace('_', ' ', $task->type)) }}</span></td>
                <td style="padding: 1.25rem 1rem; font-size: 0.9rem; color: #475569; font-weight: 700;">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                <td style="padding: 1.25rem 1rem; font-size: 0.85rem; color: #475569; font-weight: 700;">{{ $task->due_at?->format('M d, Y H:i') ?? 'No deadline' }}</td>
                <td style="padding: 1.25rem 1rem;">
                    @if($task->status === 'completed')
                        <span class="status-badge" style="background: #f0fdf4; color: #16a34a;">Completed</span>
                    @elseif($task->due_at && $task->due_at < now())
                        <span class="status-badge" style="background: #fef2f2; color: #ef4444;">Overdue</span>
                    @else
                        <span class="status-badge" style="background: #fff8e1; color: #f59e0b;">Pending</span>
                    @endif
                </td>
                <td style="padding: 1.25rem 1rem; text-align: right;">
                    @if($task->status !== 'completed')
                    <form action="{{ route('admin.crm.tasks.complete', $task) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn" title="Mark Complete" style="width: auto; padding: 0 0.75rem; font-weight: 800; color: var(--primary-green);"><i class="fas fa-check"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-clipboard-check" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Tasks Found</p>
                    <p style="font-size: 0.85rem;">Create a task to get started.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 1.5rem;">{{ $tasks->links() }}</div>
</div>
@endsection
