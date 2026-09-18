@extends('layouts.dashboard')

@section('title', 'New Deal - CRM - Forus Freight')

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.crm.pipeline') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        <i class="fas fa-arrow-left"></i> Back to Pipeline
    </a>
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">New Deal</h1>
    <p style="color: var(--text-gray); font-size: 0.9rem;">Add a new sales opportunity to the pipeline.</p>
</div>

<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); max-width: 800px;">
    <form action="{{ route('admin.crm.deals.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div style="grid-column: 1 / -1;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Deal Title *</label>
                <input type="text" name="title" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Stage *</label>
                <select name="deal_stage_id" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Priority</label>
                <select name="priority" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Value (ZMW)</label>
                <input type="number" step="0.01" name="value" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Expected Close Date</label>
                <input type="date" name="expected_close_date" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Company</label>
                <select name="company_id" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="">-- None --</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Contact</label>
                <select name="contact_id" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="">-- None --</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Assigned To</label>
                <select name="assigned_to" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; background: white;">
                    <option value="">-- Unassigned --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Source</label>
                <input type="text" name="source" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none;" placeholder="e.g. Web, Referral">
            </div>
        </div>
        <div style="margin-top: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; resize: none;"></textarea>
        </div>
        <div style="margin-top: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Notes</label>
            <textarea name="notes" rows="2" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.95rem; outline: none; resize: none;"></textarea>
        </div>
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('admin.crm.pipeline') }}" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 12px; background: var(--primary-green); color: white; border: none; font-weight: 800; cursor: pointer;"><i class="fas fa-save"></i> Create Deal</button>
        </div>
    </form>
</div>
@endsection
