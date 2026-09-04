@extends('layouts.dashboard')

@section('title', 'My Profile - Forus Freight')

@section('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 2rem;
    }

    .profile-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        text-align: center;
    }

    .avatar-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #f0fdf4;
        color: #22c55e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        position: relative;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .badge-client {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: #007f7f;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 800;
        border: 3px solid white;
    }

    .info-list {
        text-align: left;
        margin-top: 2rem;
    }

    .info-item {
        margin-bottom: 1.25rem;
    }

    .info-item label {
        font-size: 0.65rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        display: block;
        margin-bottom: 0.25rem;
    }

    .info-item p {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }

    .settings-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 600;
        background: #f8fafc;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #007f7f;
        background: white;
    }

    .btn-save {
        background: #007f7f;
        color: white;
        padding: 1rem 2rem;
        border-radius: 14px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 127, 127, 0.2);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 127, 127, 0.3);
    }

    @media (max-width: 1024px) {
        .profile-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Account Profile</h1>
    <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Manage your shipping identity and contact details.</p>
</div>

<div class="profile-grid">
    <div class="profile-card">
        <div class="avatar-wrapper">
            <i class="fas fa-user"></i>
            <div class="badge-client">{{ Auth::user()->is_admin ? 'ADMIN' : 'CLIENT' }}</div>
        </div>
        <h2 style="font-size: 1.25rem; font-weight: 900; color: #1e293b; margin-bottom: 0.25rem;">{{ Auth::user()->name }}</h2>
        <p style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Since {{ Auth::user()->created_at->format('M Y') }}</p>

        <div class="info-list">
            <div class="info-item">
                <label>Email Address</label>
                <p>{{ Auth::user()->email }}</p>
            </div>
            <div class="info-item">
                <label>Account Type</label>
                <p>Standard Client Account</p>
            </div>
            <div class="info-item">
                <label>Status</label>
                <p style="color: #22c55e;"><i class="fas fa-check-circle"></i> Verified Account</p>
            </div>
        </div>
    </div>

    <div class="settings-card">
        <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 2rem; color: #1e293b;">Edit Information</h3>
        
        @if(session('success'))
            <div style="background: #f0fdf4; color: #15803d; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; font-size: 0.9rem; border: 1px solid #bbf7d0;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('client.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required>
                    @error('name') <span style="color: #ef4444; font-size: 0.75rem; font-weight: 700;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                    @error('email') <span style="color: #ef4444; font-size: 0.75rem; font-weight: 700;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Company Name (Optional)</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', Auth::user()->company_name) }}" placeholder="e.g. Forus Freight Services">
            </div>

            <div class="form-group">
                <label>Default Shipping Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Enter your primary delivery address">{{ old('address', Auth::user()->address) }}</textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
