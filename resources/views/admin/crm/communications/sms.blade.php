@extends('layouts.dashboard')

@section('title', 'Bulk SMS - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .contact-row { transition: all 0.2s; }
    .contact-row:hover { background: #fcfdfe; }
    .contact-row td { padding: 0.75rem 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .form-control { padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.9rem; outline: none; background: white; width: 100%; }
    .form-control:focus { border-color: var(--primary-green); box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1); }
    textarea.form-control { resize: vertical; min-height: 120px; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3); }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }
    .bulk-input { min-height: 120px; font-family: monospace; font-size: 0.85rem; }
    .filter-bar { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.85rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .checkbox-custom { width: 18px; height: 18px; border-radius: 5px; border: 2px solid #cbd5e1; cursor: pointer; accent-color: var(--primary-green); }
    .char-count { font-size: 0.75rem; color: var(--text-gray); font-weight: 700; text-align: right; margin-top: 0.25rem; }
    .char-count.warning { color: #f59e0b; }
    .char-count.danger { color: #ef4444; }
    .results-bar { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
    .results-success { background: #f0fdf4; color: #16a34a; }
    .results-warning { background: #fff8e1; color: #f59e0b; }
    .results-error { background: #fef2f2; color: #ef4444; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
        div[style*="grid-template-columns: 1fr 340px"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: 1fr !important; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Bulk SMS</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Send personalized SMS campaigns to contacts and leads via Molo Marketing Cloud.</p>
        </div>
        <a href="{{ route('admin.crm.communications.whatsapp') }}" class="btn-secondary"><i class="fab fa-whatsapp"></i> Switch to WhatsApp</a>
    </div>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Total with Phone</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Leads</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">{{ $stats['leads'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Active</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--primary-green);">{{ $stats['active'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">High Value</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #8b5cf6;">{{ $stats['high_value'] }}</div>
    </div>
</div>

@if(session('bulk_results'))
@php $res = session('bulk_results'); @endphp
<div class="results-bar {{ $res['failed'] === 0 ? 'results-success' : ($res['sent'] > 0 ? 'results-warning' : 'results-error') }}">
    <i class="fas {{ $res['failed'] === 0 ? 'fa-check-circle' : ($res['sent'] > 0 ? 'fa-exclamation-circle' : 'fa-times-circle') }}"></i>
    <div>
        <strong>{{ $res['sent'] }} sent</strong> / {{ $res['total'] }} total
        @if($res['failed'] > 0) | <strong>{{ $res['failed'] }} failed</strong>@endif
    </div>
</div>
@endif

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('admin.crm.communications.sms') }}" class="{{ !request('filter') ? 'active' : '' }}">All Contacts</a>
    <a href="{{ route('admin.crm.communications.sms', ['filter' => 'leads']) }}" class="{{ request('filter') === 'leads' ? 'active' : '' }}">Leads Only</a>
    <a href="{{ route('admin.crm.communications.sms', ['filter' => 'active']) }}" class="{{ request('filter') === 'active' ? 'active' : '' }}">Active</a>
    <a href="{{ route('admin.crm.communications.sms', ['filter' => 'high_value']) }}" class="{{ request('filter') === 'high_value' ? 'active' : '' }}">High Value</a>
    <a href="{{ route('admin.crm.communications.sms', ['filter' => 'company']) }}" class="{{ request('filter') === 'company' ? 'active' : '' }}">With Company</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;">
    <!-- Left: Message Composer -->
    <div class="crm-grid">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-comment-sms" style="color: var(--primary-green);"></i> Compose Message</h2>

        <form action="{{ route('admin.crm.communications.sms.send') }}" method="POST" id="bulkSmsForm">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Selected Recipients</label>
                <div id="recipientTags" style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 12px; min-height: 40px;">
                    <span style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;">Select contacts from the list below...</span>
                </div>
                <input type="hidden" name="recipients[]" id="recipientInput">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Message</label>
                <textarea name="message" id="smsMessage" class="form-control" maxlength="640" placeholder="Type your SMS message here..." required></textarea>
                <div class="char-count" id="charCount">0 / 640 characters</div>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem; font-weight: 600;">
                    <i class="fas fa-info-circle"></i> Use <code>{name}</code>, <code>{company}</code> for personalization
                </div>
            </div>

            <!-- Bulk Number Input -->
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Bulk Numbers (Paste or Upload CSV)</label>
                <textarea name="bulk_numbers" id="bulkNumbers" class="form-control bulk-input" placeholder="Paste numbers here...&#10;Format: Name, +26097XXXXXXX&#10;Or: +26097XXXXXXX&#10;One per line."></textarea>
                <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
                    <button type="button" class="btn-secondary btn-sm" onclick="document.getElementById('csvUpload').click()">
                        <i class="fas fa-upload"></i> Upload CSV
                    </button>
                    <a href="{{ route('admin.crm.communications.download-sample-csv') }}" class="btn-secondary btn-sm" style="text-decoration:none;">
                        <i class="fas fa-download"></i> Download Sample CSV
                    </a>
                    <input type="file" id="csvUpload" accept=".csv,.txt" style="display: none;" onchange="handleCsvUpload(this)">
                    <span id="csvStatus" style="font-size: 0.75rem; color: #94a3b8; align-self: center;"></span>
                </div>
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">
                    CSV format: name,phone (header optional). Plain numbers also accepted. Auto-deduplicated.
                </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn-primary" id="sendBtn" disabled>
                    <i class="fas fa-paper-plane"></i> Send Bulk SMS
                </button>
                <span id="selectedCount" style="font-size: 0.85rem; color: var(--text-gray); font-weight: 700;">0 selected</span>
                <span id="totalRecipientsBadge" style="font-size: 0.85rem; color: var(--text-gray); font-weight: 700;">0 total</span>
            </div>
        </form>
    </div>

    <!-- Right: Contact List -->
    <div class="crm-grid" style="max-height: 700px; overflow-y: auto; overflow-x: auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 800;">Contacts <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 700;">(Page {{ $contacts->currentPage() }} of {{ $contacts->lastPage() }} — {{ $contacts->total() }} total)</span></h3>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.crm.communications.export-contacts-csv', ['filter' => request('filter')]) }}" class="btn-secondary btn-sm" style="text-decoration:none;" title="Export current filtered contacts to CSV">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <button type="button" class="btn-secondary btn-sm" onclick="selectAll()">Select All</button>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                @forelse($contacts as $contact)
                <tr class="contact-row" data-id="{{ $contact->id }}" data-phone="{{ $contact->phone }}" data-name="{{ $contact->name }}">
                    <td style="width: 30px;">
                        <input type="checkbox" class="checkbox-custom contact-check" value="{{ $contact->phone }}" data-name="{{ $contact->name }}">
                    </td>
                    <td>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-dark);">{{ $contact->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $contact->phone }}</div>
                    </td>
                    <td style="text-align: right;">
                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $contact->crm_status === 'lead' ? '#e3f2fd' : ($contact->crm_status === 'active' ? '#f0fdf4' : ($contact->crm_status === 'high_value' ? '#f3e5f5' : '#f1f5f9')) }}; color: {{ $contact->crm_status === 'lead' ? '#1e88e5' : ($contact->crm_status === 'active' ? '#16a34a' : ($contact->crm_status === 'high_value' ? '#8e24aa' : '#475569')) }};">{{ ucfirst(str_replace('_', ' ', $contact->crm_status)) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">
                        No contacts with phone numbers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($contacts->hasPages())
        <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
            @if($contacts->onFirstPage())
                <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">« Previous</span>
            @else
                <a href="{{ $contacts->previousPageUrl() }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">« Previous</a>
            @endif

            @foreach($contacts->getUrlRange(max(1, $contacts->currentPage() - 2), min($contacts->lastPage(), $contacts->currentPage() + 2)) as $page => $url)
                @if($page === $contacts->currentPage())
                    <span style="padding: 0.5rem 0.75rem; border-radius: 6px; background: var(--primary-green); color: white; font-size: 0.8rem; font-weight: 700;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">{{ $page }}</a>
                @endif
            @endforeach

            @if($contacts->hasMorePages())
                <a href="{{ $contacts->nextPageUrl() }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">Next »</a>
            @else
                <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">Next »</span>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    const checkboxes = document.querySelectorAll('.contact-check');
    const recipientTags = document.getElementById('recipientTags');
    const recipientInput = document.getElementById('recipientInput');
    const sendBtn = document.getElementById('sendBtn');
    const selectedCount = document.getElementById('selectedCount');
    const totalRecipientsBadge = document.getElementById('totalRecipientsBadge');
    const smsMessage = document.getElementById('smsMessage');
    const charCount = document.getElementById('charCount');
    const bulkNumbers = document.getElementById('bulkNumbers');
    let selectedPhones = [];
    let bulkPhones = [];

    function updateTotals() {
        const total = new Set([...selectedPhones, ...bulkPhones]).size;
        totalRecipientsBadge.textContent = total + ' total';
        sendBtn.disabled = total === 0 || smsMessage.value.trim().length === 0;
    }

    function updateUI() {
        recipientTags.innerHTML = '';
        const all = new Set([...selectedPhones, ...bulkPhones]);
        if (all.size === 0) {
            recipientTags.innerHTML = '<span style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;">Select contacts or paste numbers below...</span>';
        } else {
            Array.from(all).slice(0, 20).forEach(phone => {
                const tag = document.createElement('span');
                tag.style.cssText = 'background: var(--primary-green-light); color: var(--primary-green); padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700;';
                tag.textContent = phone;
                recipientTags.appendChild(tag);
            });
            if (all.size > 20) {
                const more = document.createElement('span');
                more.style.cssText = 'color: #94a3b8; font-size: 0.75rem; font-weight: 600;';
                more.textContent = '+' + (all.size - 20) + ' more';
                recipientTags.appendChild(more);
            }
        }

        const existingInputs = document.querySelectorAll('input[name="recipients[]"]');
        existingInputs.forEach(i => i.remove());
        Array.from(all).forEach(phone => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'recipients[]';
            inp.value = phone;
            document.getElementById('bulkSmsForm').appendChild(inp);
        });

        selectedCount.textContent = selectedPhones.length + ' selected';
        updateTotals();
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const phone = this.value;
            if (this.checked) {
                if (!selectedPhones.includes(phone)) selectedPhones.push(phone);
            } else {
                selectedPhones = selectedPhones.filter(p => p !== phone);
            }
            updateUI();
        });
    });

    function selectAll() {
        const allPhones = Array.from(checkboxes).map(cb => cb.value);
        const allChecked = selectedPhones.length === allPhones.length;

        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });

        selectedPhones = allChecked ? [] : [...allPhones];
        updateUI();
    }

    // Bulk numbers parsing
    bulkNumbers.addEventListener('input', function() {
        const lines = this.value.split(/\r?\n/).map(l => l.trim()).filter(l => l);
        bulkPhones = [];
        lines.forEach(line => {
            let phone = line;
            if (line.includes(',')) phone = line.split(',')[1]?.trim() || line.split(',')[0]?.trim();
            if (line.includes('|')) phone = line.split('|')[1]?.trim() || line.split('|')[0]?.trim();
            phone = normalizePhone(phone);
            if (phone && !bulkPhones.includes(phone)) bulkPhones.push(phone);
        });
        updateUI();
    });

    function normalizePhone(phone) {
        phone = phone.replace(/[^\d+]/g, '');
        if (!phone) return null;
        if (phone.startsWith('0') && !phone.startsWith('+')) phone = '+260' + phone.substring(1);
        if (!phone.startsWith('+')) phone = '+' + phone;
        return phone.length >= 10 ? phone : null;
    }

    // CSV upload
    function handleCsvUpload(input) {
        const file = input.files[0];
        if (!file) return;
        const status = document.getElementById('csvStatus');
        status.textContent = 'Reading...';

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l);
            let count = 0;
            lines.forEach((line, idx) => {
                if (idx === 0 && line.toLowerCase().includes('name')) return; // skip header
                let phone = line;
                if (line.includes(',')) {
                    const parts = line.split(',');
                    phone = parts[1]?.trim() || parts[0]?.trim();
                }
                phone = normalizePhone(phone);
                if (phone && !bulkPhones.includes(phone)) {
                    bulkPhones.push(phone);
                    count++;
                }
            });
            status.textContent = count + ' numbers loaded from CSV.';
            updateUI();
        };
        reader.readAsText(file);
    }

    smsMessage.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = len + ' / 640 characters';
        charCount.classList.toggle('warning', len > 480 && len <= 600);
        charCount.classList.toggle('danger', len > 600);
        updateTotals();
    });
</script>
@endsection
