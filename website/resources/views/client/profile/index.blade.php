@extends('layouts.dashboard')

@section('title', 'My Profile - Forus Freight')

@section('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 2.5rem;
    }

    .profile-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        text-align: center;
    }

    .avatar-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--primary-green-light);
        color: var(--primary-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 1.5rem;
        position: relative;
    }

    .badge-client {
        position: absolute;
        bottom: 0;
        right: 0;
        background: var(--primary-green);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        border: 4px solid white;
    }

    .info-list {
        text-align: left;
        margin-top: 2rem;
    }

    .info-item {
        margin-bottom: 1.5rem;
    }

    .info-item label {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        display: block;
        margin-bottom: 0.25rem;
    }

    .info-item p {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .settings-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
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

    .btn-save {
        background: var(--primary-green);
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">My Account Profile</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Manage your personal information and account preferences.</p>
    </div>
</div>

<div class="profile-grid">
    <div class="profile-card">
        <div class="avatar-wrapper">
            <i class="fas fa-user"></i>
            <div class="badge-client">CLIENT</div>
        </div>
        <h2 style="font-size: 1.25rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">{{ Auth::user()->name }}</h2>
        <p style="font-size: 0.85rem; color: var(--text-gray); font-weight: 600;">Member since {{ Auth::user()->created_at->format('M Y') }}</p>

        <div class="info-list">
            <div class="info-item">
                <label>Email Address</label>
                <p>{{ Auth::user()->email }}</p>
            </div>
            <div class="info-item">
                <label>Phone Number</label>
                <p>{{ Auth::user()->phone ?? 'Not provided' }}</p>
            </div>
            <div class="info-item">
                <label>Account Status</label>
                <p style="color: var(--primary-green);"><i class="fas fa-check-circle"></i> Fully Verified</p>
            </div>
        </div>
    </div>

    <div class="settings-card">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 2rem; color: var(--text-dark);">Personal Information</h3>
        
        @if(session('success'))
            <div style="background: #f0fdf4; color: #166534; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 700; font-size: 0.9rem;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 700; font-size: 0.9rem;">
                <i class="fas fa-exclamation-circle"></i> Please correct the errors below.
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
                <label>Corporate Company (Optional)</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', Auth::user()->company_name) }}" placeholder="e.g. Forus Logistics Ltd">
            </div>

            <div class="form-group">
                <label>Primary Shipping Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Enter your default delivery address">{{ old('address', Auth::user()->address) }}</textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-save">Update Profile Information</button>
            </div>
        </form>
    </div>
</div>
@endsection
