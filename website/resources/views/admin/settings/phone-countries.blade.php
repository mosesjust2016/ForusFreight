@extends('layouts.dashboard')

@section('title', 'Phone Country Whitelist - Forus Freight')

@section('styles')
<style>
    .country-card {
        background: white;
        border-radius: 16px;
        padding: 1.1rem 1.5rem;
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
    .country-card:hover { border-color: #4caf50; }
    .country-card.disabled { opacity: 0.55; background: #f8fafc; }
    .country-left { display: flex; align-items: center; gap: 0.85rem; }
    .country-flag { font-size: 1.75rem; line-height: 1; }
    .country-name { font-weight: 700; font-size: 0.95rem; }
    .country-code { font-size: 0.78rem; color: #64748b; font-family: monospace; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.65rem; border-radius: 6px;
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.03em; text-transform: uppercase;
    }
    .badge-active   { background: #f0fdf4; color: #16a34a; }
    .badge-inactive { background: #fef2f2; color: #dc2626; }
    .country-actions { display: flex; align-items: center; gap: 0.5rem; }
    .btn-toggle {
        padding: 0.35rem 0.9rem; border-radius: 8px; font-size: 0.8rem;
        font-weight: 700; border: none; cursor: pointer; transition: background 0.2s;
    }
    .btn-toggle.enable  { background: #f0fdf4; color: #16a34a; }
    .btn-toggle.disable { background: #fff7ed; color: #ea580c; }
    .btn-toggle:hover   { filter: brightness(0.93); }
    .btn-delete {
        background: none; border: none; cursor: pointer;
        color: #94a3b8; font-size: 0.8rem; padding: 0.35rem 0.5rem;
        border-radius: 6px; transition: color 0.2s, background 0.2s;
    }
    .btn-delete:hover { color: #dc2626; background: #fef2f2; }
    .add-form {
        background: white; border-radius: 16px; padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04); border: 1.5px solid #f1f5f9;
    }
    .form-row { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .form-label { font-size: 0.75rem; font-weight: 700; color: #475569; }
    .form-input {
        font-size: 0.85rem; border-radius: 8px; border: 1.5px solid #e2e8f0;
        padding: 0.45rem 0.85rem; outline: none; background: #f8fafc;
        transition: border-color 0.2s;
    }
    .form-input:focus { border-color: #4caf50; background: white; }
    .btn-add {
        background: #4caf50; color: white; border: none;
        padding: 0.45rem 1.25rem; border-radius: 8px; font-size: 0.85rem;
        font-weight: 700; cursor: pointer; transition: background 0.2s;
    }
    .btn-add:hover { background: #43a047; }
    .section-label {
        font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        color: #94a3b8; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem;
    }
    .info-box {
        background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8;
        border-radius: 10px; padding: 0.85rem 1.1rem; font-size: 0.82rem;
        margin-bottom: 1.5rem; display: flex; gap: 0.6rem; align-items: flex-start;
    }
</style>
@endsection

@section('content')
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:0.25rem;">Phone Country Whitelist</h1>
        <p style="color:#64748b; font-size:0.88rem;">Only mobile numbers from active countries below will be accepted at registration.</p>
    </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; margin-bottom:1.5rem; font-size:0.88rem; font-weight:600;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:0.75rem 1.25rem; border-radius:10px; margin-bottom:1.5rem; font-size:0.88rem; font-weight:600;">
    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
</div>
@endif

<div class="info-box">
    <i class="fas fa-circle-info" style="margin-top:0.1rem; flex-shrink:0;"></i>
    <span>Numbers must be entered in international format without spaces, e.g. <strong>+260961234567</strong>. Disabling a country blocks new registrations but does not affect existing accounts.</span>
</div>

{{-- Country list --}}
<div class="section-label">Registered Countries ({{ $countries->count() }})</div>

@forelse($countries as $country)
<div class="country-card {{ $country->is_active ? '' : 'disabled' }}">
    <div class="country-left">
        <span class="country-flag">{{ $country->flag ?? '🌐' }}</span>
        <div>
            <div class="country-name">{{ $country->name }}</div>
            <div class="country-code">+{{ $country->dial_code }}</div>
        </div>
        <span class="status-badge {{ $country->is_active ? 'badge-active' : 'badge-inactive' }}">
            <i class="fas fa-{{ $country->is_active ? 'circle-check' : 'circle-xmark' }}"></i>
            {{ $country->is_active ? 'Active' : 'Disabled' }}
        </span>
    </div>

    <div class="country-actions">
        <form method="POST" action="{{ route('admin.settings.phone-countries.toggle', $country) }}">
            @csrf
            <button type="submit" class="btn-toggle {{ $country->is_active ? 'disable' : 'enable' }}">
                {{ $country->is_active ? 'Disable' : 'Enable' }}
            </button>
        </form>

        <form method="POST" action="{{ route('admin.settings.phone-countries.destroy', $country) }}"
              onsubmit="return confirm('Remove {{ $country->name }} permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-delete" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</div>
@empty
<div style="text-align:center; padding:3rem; color:#94a3b8;">
    <i class="fas fa-earth-africa" style="font-size:2.5rem; margin-bottom:1rem; opacity:0.3;"></i>
    <p>No countries configured. Add one below.</p>
</div>
@endforelse

{{-- Add country form --}}
<div class="section-label" style="margin-top:2rem;">Add a Country</div>
<div class="add-form">
    <p style="font-size:0.82rem; color:#64748b; margin-bottom:1.1rem;">Enter the country name, dial code (digits only, no +), and optionally its flag emoji.</p>
    <form method="POST" action="{{ route('admin.settings.phone-countries.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Country Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Kenya" value="{{ old('name') }}" required style="min-width:180px;">
            </div>
            <div class="form-group">
                <label class="form-label">Dial Code (no +)</label>
                <input type="text" name="dial_code" class="form-input" placeholder="e.g. 254" value="{{ old('dial_code') }}" required style="width:110px;" pattern="\d+">
            </div>
            <div class="form-group">
                <label class="form-label">Flag Emoji <span style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                <input type="text" name="flag" class="form-input" placeholder="🇰🇪" value="{{ old('flag') }}" style="width:80px;">
            </div>
            <div class="form-group" style="justify-content:flex-end;">
                <button type="submit" class="btn-add"><i class="fas fa-plus"></i> Add Country</button>
            </div>
        </div>
    </form>
</div>
@endsection
