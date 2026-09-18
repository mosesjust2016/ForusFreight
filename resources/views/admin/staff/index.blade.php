@extends('layouts.dashboard')

@section('title', 'Staff & Role Management - Forus Freight')

@section('styles')
<style>
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.7rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .badge-super-admin { background: #fef2f2; color: #dc2626; }
    .badge-admin-staff  { background: #eff6ff; color: #2563eb; }
    .badge-sales        { background: #f0fdf4; color: #16a34a; }
    .staff-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1.5px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
        transition: border-color 0.2s;
    }
    .staff-card:hover { border-color: #4caf50; }
    .staff-left { display: flex; align-items: center; gap: 1rem; }
    .staff-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        object-fit: cover; flex-shrink: 0;
    }
    .staff-name { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.15rem; }
    .staff-email { font-size: 0.78rem; color: #64748b; }
    .staff-roles { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .staff-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    .btn-remove-role {
        background: none; border: none; cursor: pointer;
        color: #94a3b8; font-size: 0.75rem; padding: 0;
        margin-left: 0.25rem; transition: color 0.2s;
    }
    .btn-remove-role:hover { color: #dc2626; }
    .assign-form { display: flex; gap: 0.5rem; align-items: center; }
    .assign-select {
        font-size: 0.8rem; border-radius: 8px; border: 1.5px solid #e2e8f0;
        padding: 0.35rem 0.75rem; outline: none; background: #f8fafc;
    }
    .btn-assign {
        background: #4caf50; color: white; border: none;
        padding: 0.35rem 1rem; border-radius: 8px; font-size: 0.8rem;
        font-weight: 700; cursor: pointer; transition: background 0.2s;
    }
    .btn-assign:hover { background: #43a047; }
    .section-label {
        font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        color: #94a3b8; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem;
    }
    .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
    .perm-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.5rem; margin-top: 0.5rem;
    }
    .perm-item {
        font-size: 0.75rem; background: #f8fafc; border-radius: 6px;
        padding: 0.3rem 0.6rem; color: #475569; font-family: monospace;
    }
</style>
@endsection

@section('content')
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:0.25rem;">Staff & Role Management</h1>
        <p style="color:#64748b; font-size:0.88rem;">Control which staff members can access the portal and what they can do.</p>
    </div>
</div>


{{-- Role Legend --}}
<div style="display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap;">
    @foreach($roles as $role)
    <div style="background:white; border-radius:12px; padding:1rem 1.25rem; box-shadow:0 2px 12px rgba(0,0,0,0.04); border:1.5px solid #f1f5f9; min-width:200px;">
        <div style="font-weight:700; font-size:0.88rem; margin-bottom:0.5rem;">{{ $role->display_name }}</div>
        <div style="font-size:0.75rem; color:#64748b; margin-bottom:0.75rem;">{{ $role->description }}</div>
        <div style="font-size:0.7rem; color:#94a3b8; font-weight:700; text-transform:uppercase; margin-bottom:0.4rem;">Permissions ({{ count($role->permissions ?? []) }})</div>
        <div style="display:flex; flex-direction:column; gap:0.2rem; max-height:120px; overflow-y:auto;">
            @foreach($role->permissions ?? [] as $perm)
            <span class="perm-item">{{ $perm }}</span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

@if(session('generated_password'))
<div style="background:#fffbeb; border:1.5px solid #fde68a; border-radius:16px; padding:1.5rem; margin-bottom:2rem; display:flex; gap:1rem; align-items:flex-start;">
    <i class="fas fa-key" style="color:#d97706; font-size:1.25rem; margin-top:0.15rem;"></i>
    <div style="flex:1;">
        <div style="font-weight:800; color:#92400e; font-size:0.9rem; margin-bottom:0.35rem;">Save this password now — it won't be shown again</div>
        <p style="font-size:0.8rem; color:#78350f; margin-bottom:0.75rem;">Share it securely with {{ session('generated_password_email') }}. They should change it after their first login.</p>
        <div style="display:flex; gap:0.5rem; align-items:center;">
            <code id="generatedPassword" style="background:white; border:1.5px solid #fde68a; border-radius:8px; padding:0.5rem 1rem; font-size:0.9rem; font-weight:700; color:#1e293b; letter-spacing:0.02em;">{{ session('generated_password') }}</code>
            <button type="button" class="btn-assign" style="background:#d97706;" onclick="navigator.clipboard.writeText(document.getElementById('generatedPassword').textContent); this.innerHTML='<i class=\'fas fa-check\'></i> Copied'">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
</div>
@endif

{{-- Create System User --}}
<div class="section-label">Create System User</div>
<div style="background:white; border-radius:16px; padding:1.5rem; box-shadow:0 2px 12px rgba(0,0,0,0.04); border:1.5px solid #f1f5f9; margin-bottom:0.75rem;">
    <p style="font-size:0.85rem; color:#64748b; margin-bottom:1rem;">Create a new internal account for an employee — this is separate from client (customer) accounts. Assign them a role below to control what they can do in the admin portal.</p>
    <form method="POST" action="{{ route('admin.staff.store') }}" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:flex-end;">
        @csrf
        <div>
            <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.35rem;">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="assign-select" style="min-width:200px;" placeholder="e.g. Grace Milumbe" required>
            @error('name')<div style="color:#dc2626; font-size:0.72rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.35rem;">Work Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="assign-select" style="min-width:220px;" placeholder="name@forusfl.co.zm" required>
            @error('email')<div style="color:#dc2626; font-size:0.72rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:0.35rem;">Role</label>
            <select name="role_id" class="assign-select" required>
                <option value="">Select role...</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                @endforeach
            </select>
            @error('role_id')<div style="color:#dc2626; font-size:0.72rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn-assign" style="padding:0.45rem 1.25rem;">
            <i class="fas fa-user-plus"></i> Create User
        </button>
    </form>
</div>

{{-- Current Staff --}}
<div class="section-label">Current Staff ({{ $staffUsers->count() }})</div>

@forelse($staffUsers as $user)
<div class="staff-card">
    <div class="staff-left">
        <img class="staff-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4caf50&color=fff" alt="{{ $user->name }}">
        <div>
            <div class="staff-name">{{ $user->name }}</div>
            <div class="staff-email">{{ $user->email }}</div>
        </div>
    </div>

    <div class="staff-roles">
        @if($user->is_admin)
            <span class="role-badge badge-super-admin"><i class="fas fa-crown"></i> Super Admin</span>
        @endif
        @foreach($user->roles as $role)
            <span class="role-badge {{ $role->name === 'admin_staff' ? 'badge-admin-staff' : 'badge-sales' }}">
                <i class="fas fa-{{ $role->name === 'admin_staff' ? 'user-shield' : 'briefcase' }}"></i>
                {{ $role->display_name }}
                @if(!$user->is_admin)
                <form method="POST" action="{{ route('admin.staff.roles.remove', [$user, $role]) }}" style="display:inline;" onsubmit="return confirm('Remove {{ $role->display_name }} from {{ $user->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-remove-role" title="Remove role"><i class="fas fa-times"></i></button>
                </form>
                @endif
            </span>
        @endforeach
        @if(!$user->is_admin && $user->roles->isEmpty())
            <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">No roles</span>
        @endif
    </div>

    @if(!$user->is_admin)
    <div class="staff-actions">
        <form method="POST" action="{{ route('admin.staff.roles.assign', $user) }}" class="assign-form">
            @csrf
            <select name="role_id" class="assign-select" required>
                <option value="">Add role...</option>
                @foreach($roles as $role)
                    @if(!$user->roles->contains('id', $role->id))
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endif
                @endforeach
            </select>
            <button type="submit" class="btn-assign">Assign</button>
        </form>
    </div>
    @endif
</div>
@empty
<div class="empty-state">
    <i class="fas fa-users" style="font-size:2.5rem; margin-bottom:1rem; opacity:0.3;"></i>
    <p>No staff users found.</p>
</div>
@endforelse
@endsection
