@extends('layouts.dashboard')

@section('title', 'Register New Client - Forus Freight')

@section('styles')
<style>
    .create-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.05em;
    }

    .form-input {
        width: 100%;
        padding: 0.85rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
        outline: none;
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: var(--primary-green);
        background: #fcfdfe;
    }

    .submit-btn {
        background: var(--primary-green);
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem; max-width: 800px; margin: 0 auto 2rem auto;">
    <a href="{{ route('admin.clients') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Cancel and Return
    </a>
</div>

<div class="create-card">
    <div style="margin-bottom: 3rem;">
        <h1 style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">Register New Lead</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Add a client to the CRM. A full transaction account will be activated once they ship.</p>
    </div>

    <form action="{{ route('admin.clients.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="form-group">
                <label class="form-label">Full Name / Company</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. John Doe Logistics" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="client@example.com" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-input" placeholder="+260 ...">
            </div>
            <div class="form-group">
                <label class="form-label">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" class="form-input" placeholder="+260 ... (Optional)">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Initial Pipeline Status</label>
            <select name="crm_status" class="form-input" required>
                <option value="lead" selected>New Lead / Prospect</option>
                <option value="active">Pre-Approved Active</option>
                <option value="high_value">High Value Target</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Internal Registration Notes</label>
            <textarea name="internal_notes" class="form-input" style="height: 120px; resize: none;" placeholder="Details about how this client was acquired..."></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 2rem; border-top: 2px solid #f8fafc; padding-top: 2rem;">
            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Register CRM Lead
            </button>
        </div>
    </form>
</div>
@endsection
