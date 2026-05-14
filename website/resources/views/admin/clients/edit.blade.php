@extends('layouts.dashboard')

@section('title', 'Edit Client - ' . $user->name)

@section('styles')
<style>
    .edit-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #f1f5f9;
        border-radius: 15px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s;
        outline: none;
    }

    .form-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px var(--primary-green-light);
    }

    .submit-btn {
        background: var(--primary-green);
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 15px;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .submit-btn:hover {
        background: #005a59;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 112, 111, 0.2);
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem; max-width: 800px; margin: 0 auto 2rem auto;">
    <a href="{{ route('admin.clients') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Cancel and Return
    </a>
</div>

<div class="edit-card">
    <div style="margin-bottom: 3rem;">
        <h1 style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">Edit Client Information</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Update the account details for {{ $user->name }}.</p>
    </div>

    <form action="{{ route('admin.clients.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-input" value="{{ $user->phone }}" placeholder="+260 ...">
            </div>
            <div class="form-group">
                <label class="form-label">Verification Status</label>
                <select name="status" class="form-input">
                    <option value="verified">Verified</option>
                    <option value="unverified">Unverified</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Admin Notes</label>
            <textarea name="notes" class="form-input" style="height: 120px; resize: none;" placeholder="Internal notes about this client...">{{ $user->internal_notes }}</textarea>
        </div>

        <!-- CRM Section -->
        <div style="margin-top: 4rem; padding-top: 3rem; border-top: 2px dashed #f1f5f9; position: relative;">
            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #f8fafc; padding: 0 1.5rem; color: #64748b; font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em;">
                <i class="fas fa-shield-halved"></i> CRM & Business Logic
            </div>

            @php $isLocked = !request()->has('unlock'); @endphp

            @if($isLocked)
                <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 15px; padding: 1.5rem; margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fbbf24; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; color: #92400e;">CRM Records Locked</div>
                            <div style="font-size: 0.8rem; color: #b45309; font-weight: 600;">System admin credentials required to modify financial and status logic.</div>
                        </div>
                    </div>
                    <button type="button" onclick="openSecurityModal()" style="background: #1e293b; color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 800; font-size: 0.75rem; cursor: pointer;">
                        UNLOCK NOW
                    </button>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group">
                    <label class="form-label">Client Pipeline Status</label>
                    <select name="crm_status" class="form-input" {{ $isLocked ? 'disabled' : '' }}>
                        <option value="lead" {{ $user->crm_status === 'lead' ? 'selected' : '' }}>Lead / New Prospect</option>
                        <option value="active" {{ $user->crm_status === 'active' ? 'selected' : '' }}>Active Account</option>
                        <option value="high_value" {{ $user->crm_status === 'high_value' ? 'selected' : '' }}>High Value Partner</option>
                        <option value="inactive" {{ $user->crm_status === 'inactive' ? 'selected' : '' }}>Inactive / Dormant</option>
                        <option value="blocked" {{ $user->crm_status === 'blocked' ? 'selected' : '' }}>Credit Blocked</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Credit Facility (ZMW)</label>
                    <input type="number" name="credit_limit" class="form-input" value="{{ $user->credit_limit }}" {{ $isLocked ? 'disabled' : '' }}>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group">
                    <label class="form-label">Assigned Account Manager</label>
                    <input type="text" name="assigned_agent" class="form-input" value="{{ $user->assigned_agent }}" placeholder="Manager Name" {{ $isLocked ? 'disabled' : '' }}>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Terms</label>
                    <select name="payment_terms" class="form-input" {{ $isLocked ? 'disabled' : '' }}>
                        <option value="prepaid" {{ $user->payment_terms === 'prepaid' ? 'selected' : '' }}>Prepaid Only</option>
                        <option value="net15" {{ $user->payment_terms === 'net15' ? 'selected' : '' }}>Net 15 Days</option>
                        <option value="net30" {{ $user->payment_terms === 'net30' ? 'selected' : '' }}>Net 30 Days</option>
                        <option value="cod" {{ $user->payment_terms === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 2rem; border-top: 2px solid #f8fafc; padding-top: 2rem;">
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<!-- Security Verification Modal -->
<div id="securityModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; width: 100%; max-width: 400px; border-radius: 30px; padding: 3rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); text-align: center;">
        <div style="width: 70px; height: 70px; background: #fef2f2; color: #ef4444; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 2rem auto;">
            <i class="fas fa-user-shield"></i>
        </div>
        
        <h2 style="font-size: 1.5rem; font-weight: 900; color: #1e293b; margin-bottom: 0.5rem;">Security Gateway</h2>
        <p style="color: #64748b; font-size: 0.9rem; font-weight: 600; margin-bottom: 2rem;">Please enter your System Admin PIN to access sensitive CRM data.</p>

        <div style="margin-bottom: 2rem;">
            <input type="password" id="adminPin" maxlength="4" placeholder="••••" style="width: 100%; text-align: center; font-size: 2rem; letter-spacing: 0.5em; padding: 1rem; border: 2px solid #f1f5f9; border-radius: 15px; font-weight: 900; outline: none; transition: all 0.3s;" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <div id="pinError" style="display: none; color: #ef4444; font-size: 0.75rem; font-weight: 800; margin-top: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                <i class="fas fa-circle-exclamation"></i> Invalid Security PIN
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button onclick="closeSecurityModal()" style="flex: 1; background: #f1f5f9; color: #475569; padding: 1rem; border: none; border-radius: 15px; font-weight: 800; cursor: pointer;">CANCEL</button>
            <button onclick="verifyPin()" style="flex: 2; background: #1e293b; color: white; padding: 1rem; border: none; border-radius: 15px; font-weight: 800; cursor: pointer;">VERIFY IDENTITY</button>
        </div>
    </div>
</div>

<script>
    function openSecurityModal() {
        document.getElementById('securityModal').style.display = 'flex';
        document.getElementById('adminPin').focus();
    }

    function closeSecurityModal() {
        document.getElementById('securityModal').style.display = 'none';
        document.getElementById('adminPin').value = '';
        document.getElementById('pinError').style.display = 'none';
    }

    function verifyPin() {
        const pin = document.getElementById('adminPin').value;
        const error = document.getElementById('pinError');
        if(pin === '1234') {
            window.location.search = '?unlock=true';
        } else {
            error.style.display = 'block';
            document.getElementById('adminPin').value = '';
            document.getElementById('adminPin').focus();
        }
    }
</script>
@endsection

