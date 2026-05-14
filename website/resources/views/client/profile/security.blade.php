@extends('layouts.dashboard')

@section('title', 'Security Settings - Forus Freight')

@section('styles')
<style>
    .security-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .security-card {
        background: white;
        border-radius: 30px;
        padding: 3.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2.5rem;
    }

    .security-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .security-header i {
        width: 50px;
        height: 50px;
        background: #fef2f2;
        color: #ef4444;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .security-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.85rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        background: #fcfdfe;
    }

    .btn-update {
        background: #1e293b;
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 12px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-update:hover {
        background: #0f172a;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Security & Privacy</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Maintain your account safety by updating your password and security protocols.</p>
    </div>
</div>

<div class="security-container">
    @if(session('status') === 'password-updated')
        <div style="background: #f0fdf4; border-left: 4px solid #16a34a; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; color: #166534; font-weight: 700; font-size: 0.85rem;">
            <i class="fas fa-check-circle"></i> Password has been updated successfully.
        </div>
    @endif

    <div class="security-card">
        <div class="security-header">
            <i class="fas fa-key"></i>
            <h3>Update Password</h3>
        </div>

        <form action="{{ route('client.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password', 'updatePassword')
                    <span style="color: #ef4444; font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password', 'updatePassword')
                    <span style="color: #ef4444; font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-update">Update Security Credentials</button>
            </div>
        </form>
    </div>

    <div class="security-card" style="opacity: 0.7;">
        <div class="security-header" style="margin-bottom: 1.5rem;">
            <i class="fas fa-shield-halved" style="background: #eff6ff; color: #2563eb;"></i>
            <h3>Two-Factor Authentication</h3>
        </div>
        <p style="font-size: 0.85rem; color: var(--text-gray); margin-bottom: 1.5rem;">Add an extra layer of security to your account by enabling 2FA. (Coming Soon)</p>
        <button disabled style="background: #e2e8f0; color: #94a3b8; padding: 0.75rem 1.5rem; border-radius: 10px; border: none; font-weight: 800; cursor: not-allowed;">Configure 2FA</button>
    </div>
</div>
@endsection
