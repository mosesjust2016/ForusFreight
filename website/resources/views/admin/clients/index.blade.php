@extends('layouts.dashboard')

@section('title', 'Client Management - Forus Freight')

@section('styles')
<style>
    .admin-client-grid {
        background: white;
        border-radius: 30px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .client-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 1rem;
    }

    .client-table th {
        text-align: left;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        border-bottom: 2px solid #f8fafc;
    }

    .client-row {
        background: #fff;
        transition: all 0.3s;
    }

    .client-row:hover {
        background: #fcfdfe;
    }

    .client-row td {
        padding: 1.5rem 1rem;
        border-top: 1px solid #f8fafc;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .client-row td:first-child {
        border-left: 1px solid #f8fafc;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }

    .client-row td:last-child {
        border-right: 1px solid #f8fafc;
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .client-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .client-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.1rem;
    }

    .badge-verified {
        background: #f0fdf4;
        color: #16a34a;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .stat-pill {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        background: #f1f5f9;
        color: #475569;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .action-group {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #1e293b;
        color: white;
    }

    /* CRM Dropdown Styling */
    .crm-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 280px;
        background: white;
        border-radius: 18px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        border: 1px solid #f1f5f9;
        z-index: 1000;
        padding: 0.75rem;
        margin-top: 0.5rem;
        animation: slideIn 0.2s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-header {
        font-size: 0.65rem;
        font-weight: 900;
        color: #94a3b8;
        padding: 0.75rem 1rem 0.5rem 1rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .dropdown-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        color: #334155;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 700;
        border-radius: 12px;
        transition: all 0.2s;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        color: var(--primary-green);
    }

    .dropdown-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 0.5rem 0;
    }

    .crm-mini-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 0.5rem;
        border: 1px solid #edf2f7;
    }

    .crm-stat {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .crm-stat span:first-child {
        color: #64748b;
        font-weight: 700;
    }

    .status-pill-mini {
        padding: 0.2rem 0.5rem;
        background: #fffbeb;
        color: #b45309;
        border-radius: 6px;
        font-size: 0.6rem;
        font-weight: 900;
    }

    .crm-unlock-btn {
        width: 100%;
        background: #1e293b;
        color: white;
        border: none;
        padding: 0.6rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
        margin-top: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .crm-unlock-btn:hover {
        background: var(--primary-green);
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Client Directory</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Manage and monitor all registered freight clients and their activity.</p>
        </div>
        <a href="{{ route('admin.clients.create') }}" style="background: var(--primary-green); color: white; padding: 0.85rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-decoration: none;">
            <i class="fas fa-user-plus"></i> Register New Client
        </a>
    </div>
</div>

<!-- Status Filter Tabs -->
<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="{{ route('admin.clients') }}"
       style="padding: 0.6rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;
       {{ $currentStatus === 'all' ? 'background: #1e293b; color: white; box-shadow: 0 4px 12px rgba(30,41,59,0.25);' : 'background: white; color: #64748b; border: 1.5px solid #e2e8f0;' }}">
        <i class="fas fa-users" style="margin-right: 0.4rem;"></i> All
        <span style="{{ $currentStatus === 'all' ? 'background: rgba(255,255,255,0.2);' : 'background: #f1f5f9;' }} padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.7rem; margin-left: 0.4rem;">{{ $statusCounts['all'] }}</span>
    </a>
    <a href="{{ route('admin.clients', ['status' => 'lead']) }}"
       style="padding: 0.6rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;
       {{ $currentStatus === 'lead' ? 'background: #f59e0b; color: white; box-shadow: 0 4px 12px rgba(245,158,11,0.25);' : 'background: white; color: #64748b; border: 1.5px solid #e2e8f0;' }}">
        <i class="fas fa-circle" style="margin-right: 0.4rem; font-size: 0.5rem; vertical-align: middle;"></i> Leads
        <span style="{{ $currentStatus === 'lead' ? 'background: rgba(255,255,255,0.25);' : 'background: #fffbeb; color: #b45309;' }} padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.7rem; margin-left: 0.4rem;">{{ $statusCounts['lead'] }}</span>
    </a>
    <a href="{{ route('admin.clients', ['status' => 'active']) }}"
       style="padding: 0.6rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;
       {{ $currentStatus === 'active' ? 'background: #22c55e; color: white; box-shadow: 0 4px 12px rgba(34,197,94,0.25);' : 'background: white; color: #64748b; border: 1.5px solid #e2e8f0;' }}">
        <i class="fas fa-circle" style="margin-right: 0.4rem; font-size: 0.5rem; vertical-align: middle;"></i> Active
        <span style="{{ $currentStatus === 'active' ? 'background: rgba(255,255,255,0.25);' : 'background: #f0fdf4; color: #15803d;' }} padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.7rem; margin-left: 0.4rem;">{{ $statusCounts['active'] }}</span>
    </a>
    <a href="{{ route('admin.clients', ['status' => 'high_value']) }}"
       style="padding: 0.6rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;
       {{ $currentStatus === 'high_value' ? 'background: #8b5cf6; color: white; box-shadow: 0 4px 12px rgba(139,92,246,0.25);' : 'background: white; color: #64748b; border: 1.5px solid #e2e8f0;' }}">
        <i class="fas fa-gem" style="margin-right: 0.4rem;"></i> High Value
        <span style="{{ $currentStatus === 'high_value' ? 'background: rgba(255,255,255,0.25);' : 'background: #f5f3ff; color: #6d28d9;' }} padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.7rem; margin-left: 0.4rem;">{{ $statusCounts['high_value'] }}</span>
    </a>
    <a href="{{ route('admin.clients', ['status' => 'blocked']) }}"
       style="padding: 0.6rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s;
       {{ $currentStatus === 'blocked' ? 'background: #ef4444; color: white; box-shadow: 0 4px 12px rgba(239,68,68,0.25);' : 'background: white; color: #64748b; border: 1.5px solid #e2e8f0;' }}">
        <i class="fas fa-ban" style="margin-right: 0.4rem;"></i> Blocked
        <span style="{{ $currentStatus === 'blocked' ? 'background: rgba(255,255,255,0.25);' : 'background: #fef2f2; color: #991b1b;' }} padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.7rem; margin-left: 0.4rem;">{{ $statusCounts['blocked'] }}</span>
    </a>
</div>

<div class="admin-client-grid">
    <table class="client-table">
        <thead>
            <tr>
                <th>Client Details</th>
                <th>Contact Info</th>
                <th>Shipments</th>
                <th>Financials</th>
                <th>Joined Date</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr class="client-row">
                    <td>
                        <div class="client-info">
                            <div class="client-avatar">{{ substr($client->name, 0, 1) }}</div>
                            <div>
                                <div style="font-weight: 800; color: var(--text-dark); font-size: 1rem;">{{ $client->name }}</div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                    <span class="badge-verified"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                    @if($client->is_admin)
                                        <span style="background: #eff6ff; color: #1d4ed8; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.6rem; font-weight: 800;">ADMIN</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">{{ $client->email }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-gray); font-weight: 600;">{{ $client->phone ?? 'No phone' }}</div>
                    </td>
                    <td>
                        <div class="stat-pill">
                            <i class="fas fa-box" style="margin-right: 0.4rem;"></i> {{ $client->shipments->count() }} Orders
                        </div>
                    </td>
                    <td>
                        @php
                            $totalSpent = $client->shipments->sum('total_charge');
                        @endphp
                        <div style="font-weight: 900; color: var(--text-dark);">{{ number_format($totalSpent, 2) }} ZMW</div>
                        <div style="font-size: 0.7rem; color: var(--text-gray); font-weight: 700;">LIFETIME VALUE</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">{{ $client->created_at->format('d M, Y') }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $client->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="action-group" style="position: relative;">
                            <!-- Main Actions -->
                            <a href="{{ route('admin.clients.show', $client) }}" class="action-btn" title="Quick View"><i class="fas fa-eye"></i></a>
                            
                            <!-- Dropdown Trigger -->
                            <button onclick="toggleActionDropdown('{{ $client->id }}')" class="action-btn" style="background: #1e293b; color: white;">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>

                            <!-- Professional CRM & Action Dropdown -->
                            <div id="dropdown-{{ $client->id }}" class="crm-dropdown" style="display: none;">
                                <div class="dropdown-header">MANAGEMENT HUB</div>
                                <a href="{{ route('admin.clients.edit', $client) }}" class="dropdown-item">
                                    <i class="fas fa-user-pen"></i> Edit Profile
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">COMMUNICATIONS</div>
                                <button onclick="openMessageModal('email', '{{ $client->email }}', '{{ $client->name }}', '{{ $client->id }}')" class="dropdown-item">
                                    <i class="fas fa-envelope" style="color: #3b82f6;"></i> Send Email
                                </button>
                                @if($client->phone)
                                    <button onclick="openMessageModal('sms', '{{ $client->phone }}', '{{ $client->name }}', '{{ $client->id }}')" class="dropdown-item">
                                        <i class="fas fa-comment-sms" style="color: #f59e0b;"></i> Send SMS
                                    </button>
                                    <button onclick="openMessageModal('whatsapp', '{{ $client->phone }}', '{{ $client->name }}', '{{ $client->id }}')" class="dropdown-item">
                                        <i class="fab fa-whatsapp" style="color: #22c55e;"></i> WhatsApp
                                    </button>
                                @endif

                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">CRM PORTFOLIO</div>
                                <div class="crm-mini-card">
                                    <div class="crm-stat">
                                        <span>Status</span>
                                        <span class="status-pill-mini">{{ strtoupper($client->crm_status) }}</span>
                                    </div>
                                    <div class="crm-stat">
                                        <span>Limit</span>
                                        <span style="font-weight: 800;">{{ number_format($client->credit_limit, 0) }} ZMW</span>
                                    </div>
                                    <button onclick="unlockCRM('{{ $client->id }}')" class="crm-unlock-btn">
                                        <i class="fas fa-lock-open"></i> UNLOCK CRM
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 5rem 0; color: var(--text-gray);">
                        <i class="fas fa-users-slash" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                        <h3 style="font-weight: 800;">
                            {{ $currentStatus !== 'all' ? 'No ' . ucfirst(str_replace('_', ' ', $currentStatus)) . ' Clients' : 'No Clients Registered' }}
                        </h3>
                        <p style="font-size: 0.9rem;">
                            {{ $currentStatus !== 'all' ? 'There are no clients with this status in the system.' : 'Start by inviting or registering your first logistics client.' }}
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div style="margin-top: 2.5rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
        {{-- Previous Page Link --}}
        @if ($clients->onFirstPage())
            <span style="padding: 0.6rem 0.8rem; border-radius: 8px; color: #cbd5e1; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $clients->previousPageUrl() }}" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #f8fafc; color: var(--primary-green); text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
            @if ($page == $clients->currentPage())
                <span style="padding: 0.6rem 0.8rem; border-radius: 8px; background: var(--primary-green); color: white; font-weight: 700; min-width: 2.5rem; text-align: center;">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #f8fafc; color: var(--text-dark); text-decoration: none; transition: all 0.3s; min-width: 2.5rem; text-align: center;">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($clients->hasMorePages())
            <a href="{{ $clients->nextPageUrl() }}" style="padding: 0.6rem 0.8rem; border-radius: 8px; background: #f8fafc; color: var(--primary-green); text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span style="padding: 0.6rem 0.8rem; border-radius: 8px; color: #cbd5e1; cursor: not-allowed;">
                <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>

    {{-- Pagination Info --}}
    <div style="margin-top: 1.5rem; text-align: center; color: #64748b; font-size: 0.9rem;">
        Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients
        (Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }})
    </div>
    @endif
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
            <input type="hidden" id="msgUserId" name="user_id">
            
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
    let activeClientId = null;

    function openMessageModal(type, recipient, name, clientId) {
        const modal = document.getElementById('messageModal');
        const title = document.getElementById('modalTitle');
        const btnText = document.getElementById('btnText');
        const submitBtn = document.getElementById('submitBtn');
        
        document.getElementById('msgType').value = type;
        document.getElementById('msgRecipient').value = recipient;
        document.getElementById('msgUserId').value = clientId;
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

    function toggleActionDropdown(clientId) {
        const dropdown = document.getElementById('dropdown-' + clientId);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        
        allDropdowns.forEach(d => {
            if(d.id !== 'dropdown-' + clientId) d.style.display = 'none';
        });

        if(dropdown.style.display === 'none') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    function unlockCRM(clientId) {
        activeClientId = clientId;
        document.getElementById('securityModal').style.display = 'flex';
        document.getElementById('adminPin').focus();
    }

    function closeSecurityModal() {
        document.getElementById('securityModal').style.display = 'none';
        document.getElementById('adminPin').value = '';
        document.getElementById('pinError').style.display = 'none';
        activeClientId = null;
    }

    function verifyPin() {
        const pin = document.getElementById('adminPin').value;
        const error = document.getElementById('pinError');
        
        // Security PIN: 1234
        if(pin === '1234') { 
            window.location.href = "{{ url('admin/clients') }}/" + activeClientId + "/edit?unlock=true";
        } else {
            error.style.display = 'block';
            document.getElementById('adminPin').value = '';
            document.getElementById('adminPin').focus();
        }
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('messageModal');
        if (event.target == modal) {
            closeMessageModal();
        }
    }
</script>
@endsection
