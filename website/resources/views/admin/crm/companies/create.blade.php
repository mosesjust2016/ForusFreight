@extends('layouts.dashboard')

@section('title', 'Add Company - CRM - Forus Freight')

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.companies') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        <i class="fas fa-arrow-left"></i> Back to Companies
    </a>
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Add Company</h1>
    <p style="color: var(--text-gray); font-size: 0.9rem;">Create a new corporate account and assign an agent.</p>
</div>

<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); max-width: 800px;">
    <form action="{{ route('admin.crm.companies.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Company Name *</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="e.g. Acme Logistics">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Industry</label>
                <input type="text" name="industry" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="e.g. Manufacturing">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Website</label>
                <input type="url" name="website" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="https://...">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Email</label>
                <input type="email" name="email" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="contact@company.com">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Phone</label>
                <input type="text" name="phone" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="+260...">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Assigned Agent</label>
                <select name="assigned_agent_id" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="">-- Select Agent --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">City</label>
                <input type="text" name="city" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Country</label>
                <input type="text" name="country" value="Zambia" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Annual Revenue</label>
                <input type="number" step="0.01" name="annual_revenue" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="0.00">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Employees</label>
                <input type="number" name="employee_count" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="0">
            </div>
        </div>
        <div style="margin-top: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Address</label>
            <textarea name="address" rows="2" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; resize: none;"></textarea>
        </div>
        <div style="margin-top: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Notes</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; resize: none;"></textarea>
        </div>
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('admin.crm.companies') }}" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: var(--primary-green); color: white; border: none; font-weight: 800; cursor: pointer;"><i class="fas fa-save"></i> Save Company</button>
        </div>
    </form>
</div>
@endsection
