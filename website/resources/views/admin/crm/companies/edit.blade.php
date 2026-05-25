@extends('layouts.dashboard')

@section('title', 'Edit ' . $company->name . ' - CRM - Forus Freight')

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.companies.show', $company) }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        <i class="fas fa-arrow-left"></i> Back to Company
    </a>
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Edit Company</h1>
    <p style="color: var(--text-gray); font-size: 0.9rem;">Update corporate account details and manage linked contacts.</p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
    <div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow);">
        <form action="{{ route('admin.crm.companies.update', $company) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Company Name *</label>
                    <input type="text" name="name" value="{{ $company->name }}" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Status</label>
                    <select name="status" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                        <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="prospect" {{ $company->status === 'prospect' ? 'selected' : '' }}>Prospect</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Industry</label>
                    <input type="text" name="industry" value="{{ $company->industry }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Website</label>
                    <input type="url" name="website" value="{{ $company->website }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Email</label>
                    <input type="email" name="email" value="{{ $company->email }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Phone</label>
                    <input type="text" name="phone" value="{{ $company->phone }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Assigned Agent</label>
                    <select name="assigned_agent_id" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                        <option value="">-- Select Agent --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $company->assigned_agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">City</label>
                    <input type="text" name="city" value="{{ $company->city }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Annual Revenue</label>
                    <input type="number" step="0.01" name="annual_revenue" value="{{ $company->annual_revenue }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Employees</label>
                    <input type="number" name="employee_count" value="{{ $company->employee_count }}" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>
            </div>
            <div style="margin-top: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Notes</label>
                <textarea name="notes" rows="3" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; resize: none;">{{ $company->notes }}</textarea>
            </div>
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.crm.companies.show', $company) }}" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none;">Cancel</a>
                <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: var(--primary-green); color: white; border: none; font-weight: 800; cursor: pointer;"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>

    <div style="background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow);">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem;"><i class="fas fa-user-plus" style="color: var(--primary-green); margin-right: 0.5rem;"></i> Link Contact</h2>
        <form action="{{ route('admin.crm.companies.link-contact', $company) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Contact</label>
                <select name="user_id" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="">-- Select Contact --</option>
                    @foreach($availableContacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Role</label>
                <select name="role" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="contact">Contact</option>
                    <option value="manager">Manager</option>
                    <option value="decision_maker">Decision Maker</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 700; color: var(--text-dark); cursor: pointer;">
                    <input type="checkbox" name="is_primary" value="1"> Primary Contact
                </label>
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem; border-radius: 12px; background: var(--primary-green); color: white; border: none; font-weight: 800; cursor: pointer;"><i class="fas fa-link"></i> Link Contact</button>
        </form>

        <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 1.5rem 0;">

        <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;"><i class="fas fa-users" style="color: #3b82f6; margin-right: 0.5rem;"></i> Linked Contacts</h2>
        @foreach($company->contacts as $contact)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
            <div>
                <div style="font-weight: 800; font-size: 0.85rem;">{{ $contact->name }}</div>
                <div style="font-size: 0.75rem; color: var(--text-gray);">{{ $contact->pivot->role }}</div>
            </div>
            <form action="{{ route('admin.crm.companies.unlink-contact', [$company, $contact]) }}" method="POST" onsubmit="return confirm('Unlink this contact?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-unlink"></i></button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
