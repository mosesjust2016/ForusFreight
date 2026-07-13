@extends('layouts.dashboard')

@section('title', 'Client Profile - ' . $user->name)

@section('styles')
<style>
    .profile-header {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        display: grid;
        grid-template-columns: 120px 1fr auto;
        gap: 2.5rem;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 24px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 900;
    }

    .info-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
        height: 100%;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .detail-item {
        margin-bottom: 1.25rem;
    }

    .detail-label {
        font-size: 0.75rem;
        color: var(--text-gray);
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .shipment-list {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
        margin-top: 2.5rem;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    @media (max-width: 1024px) {
        .grid-3 { grid-template-columns: 1fr; }
        .profile-header { grid-template-columns: 1fr; text-align: center; justify-items: center; }
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.clients') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Client Directory
    </a>
</div>

<div class="profile-header">
    <div class="profile-avatar">{{ substr($user->name, 0, 1) }}</div>
    <div>
        <h1 style="font-size: 2rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">{{ $user->name }}</h1>
        <div style="display: flex; gap: 1.5rem; color: var(--text-gray); font-size: 0.95rem;">
            <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
            <span><i class="fas fa-phone"></i> {{ $user->phone ?? 'Not provided' }}</span>
        </div>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button onclick="openMessageModal('whatsapp', '{{ $user->phone }}', '{{ $user->name }}')" style="background: #22c55e; color: white; width: 50px; height: 50px; border-radius: 15px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem;">
            <i class="fab fa-whatsapp"></i>
        </button>
        <button onclick="openMessageModal('sms', '{{ $user->phone }}', '{{ $user->name }}')" style="background: #f59e0b; color: white; width: 50px; height: 50px; border-radius: 15px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem;">
            <i class="fas fa-comment-sms"></i>
        </button>
        <a href="{{ route('admin.clients.edit', $user) }}" style="background: var(--primary-green); color: white; padding: 0.85rem 2rem; border-radius: 15px; text-decoration: none; font-weight: 800; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-user-pen"></i> Edit Profile
        </a>
    </div>
</div>

<!-- Communication Modal -->
<div id="messageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 50px rgba(0,0,0,0.2); position: relative;">
        <button onclick="closeMessageModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; font-size: 1.25rem; color: var(--text-gray); cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>

        <div style="margin-bottom: 2rem;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.5rem;">Send Message</h2>
            <p id="modalSubtitle" style="color: var(--text-gray); font-size: 0.9rem;">To: <span id="recipientName" style="font-weight: 800; color: var(--primary-green);"></span></p>
        </div>

        <form id="messageForm" action="{{ route('admin.clients.send-message') }}" method="POST">
            @csrf
            <input type="hidden" id="msgType" name="type">
            <input type="hidden" id="msgRecipient" name="recipient">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Message Content</label>
                <textarea name="message" required style="width: 100%; height: 150px; border: 2px solid #f1f5f9; border-radius: 15px; padding: 1rem; font-size: 0.95rem; font-weight: 600; outline: none; resize: none; transition: border-color 0.3s;" placeholder="Type your message here..."></textarea>
            </div>

            <button type="submit" id="submitBtn" style="width: 100%; background: var(--primary-green); color: white; padding: 1rem; border: none; border-radius: 12px; font-weight: 800; font-size: 1rem; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
                <i class="fas fa-paper-plane"></i> <span id="btnText">Send Email</span>
            </button>
        </form>
    </div>
</div>

<script>
    function openMessageModal(type, recipient, name) {
        const modal = document.getElementById('messageModal');
        const title = document.getElementById('modalTitle');
        const btnText = document.getElementById('btnText');
        const submitBtn = document.getElementById('submitBtn');
        
        document.getElementById('msgType').value = type;
        document.getElementById('msgRecipient').value = recipient;
        document.getElementById('recipientName').innerText = name + ' (' + recipient + ')';

        if(type === 'email') {
            title.innerText = 'Send Official Email';
            btnText.innerText = 'Send Email';
            submitBtn.style.background = '#3b82f6';
        } else if(type === 'sms') {
            title.innerText = 'Send Direct SMS';
            btnText.innerText = 'Send SMS';
            submitBtn.style.background = '#f59e0b';
        } else {
            title.innerText = 'Send WhatsApp Message';
            btnText.innerText = 'Send via WhatsApp';
            submitBtn.style.background = '#22c55e';
        }

        modal.style.display = 'flex';
    }

    function closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
        document.getElementById('messageForm').reset();
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('messageModal');
        if (event.target == modal) {
            closeMessageModal();
        }
    }
</script>

<div class="grid-3">
    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-address-card" style="color: var(--primary-green);"></i> Account Details</h2>
        <div class="detail-item">
            <div class="detail-label">Member Since</div>
            <div class="detail-value">{{ $user->created_at->format('F d, Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span style="color: #16a34a;"><i class="fas fa-check-circle"></i> Fully Verified</span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Account Type</div>
            <div class="detail-value">Corporate Client</div>
        </div>
    </div>

    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-chart-line" style="color: #3b82f6;"></i> Logistics Stats</h2>
        <div class="detail-item">
            <div class="detail-label">Total Shipments</div>
            <div class="detail-value">{{ $shipments->count() }} Orders</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Active Shipments</div>
            <div class="detail-value">{{ $shipments->whereIn('status', ['In Transit', 'Out for Delivery'])->count() }} In Motion</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Completion Rate</div>
            <div class="detail-value">100%</div>
        </div>
    </div>

    <div class="info-card">
        <h2 class="section-title"><i class="fas fa-wallet" style="color: #f59e0b;"></i> Financial Summary</h2>
        <div class="detail-item">
            <div class="detail-label">Lifetime Spend</div>
            <div class="detail-value" style="font-size: 1.5rem; font-weight: 900;">{{ number_format($shipments->sum('total_charge'), 2) }} ZMW</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Last Transaction</div>
            <div class="detail-value">{{ $shipments->first() ? $shipments->first()->created_at->diffForHumans() : 'No transactions' }}</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; margin-top: 2.5rem;">
    <div class="shipment-list" style="margin-top: 0;">
        <h2 class="section-title"><i class="fas fa-box-open"></i> Recent Shipping History</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 1rem 0; font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Tracking #</th>
                    <th style="padding: 1rem 0; font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Route</th>
                    <th style="padding: 1rem 0; font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments->take(8) as $shipment)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1.25rem 0; font-weight: 700; font-family: monospace;">{{ $shipment->serial_no }}</td>
                    <td style="padding: 1.25rem 0; font-weight: 700; font-size: 0.9rem;">{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                    <td style="padding: 1.25rem 0;">
                        <span class="status-badge" style="background: #eff6ff; color: #1d4ed8;">{{ $shipment->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="info-card" style="background: #f8fafc; border: 1px solid #e2e8f0; box-shadow: none;">
        <h2 class="section-title"><i class="fas fa-history" style="color: #6366f1;"></i> Communication Logs</h2>
        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-height: 500px; overflow-y: auto; padding-right: 0.5rem;">
            @forelse($communicationLogs as $log)
                <div style="background: white; padding: 1.25rem; border-radius: 15px; border: 1px solid #edf2f7; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.65rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">
                            @if($log->type === 'email')
                                <i class="fas fa-envelope" style="color: #3b82f6;"></i> Official Email
                            @elseif($log->type === 'sms')
                                <i class="fas fa-comment-sms" style="color: #f59e0b;"></i> Direct SMS
                            @else
                                <i class="fab fa-whatsapp" style="color: #22c55e;"></i> WhatsApp
                            @endif
                        </span>
                        <span style="font-size: 0.65rem; font-weight: 700; color: #cbd5e1;">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 0.85rem; color: #475569; font-weight: 600; line-height: 1.5;">
                        "{{ Str::limit($log->message, 120) }}"
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; margin-top: 0.5rem;">Sent to: {{ $log->recipient }}</div>
                </div>
            @empty
                <div style="text-align: center; padding: 3rem 0; opacity: 0.5;">
                    <i class="fas fa-comment-slash" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <div style="font-size: 0.8rem; font-weight: 700;">No communication history</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
