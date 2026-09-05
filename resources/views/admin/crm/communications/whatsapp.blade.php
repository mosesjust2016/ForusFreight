@extends('layouts.dashboard')

@section('title', 'Bulk WhatsApp - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .contact-row { transition: all 0.2s; cursor: pointer; }
    .contact-row:hover { background: #fcfdfe; }
    .contact-row td { padding: 0.75rem 1rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .form-control { padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.9rem; outline: none; background: white; width: 100%; }
    .form-control:focus { border-color: #22c55e; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }
    textarea.form-control { resize: vertical; min-height: 120px; }
    .btn-whatsapp { background: #22c55e; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-whatsapp:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34, 197, 94, 0.3); }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }
    .btn-danger { background: #fef2f2; color: #ef4444; }
    .btn-danger:hover { background: #fee2e2; }
    .btn-warning { background: #fff8e1; color: #f59e0b; }
    .btn-warning:hover { background: #fef3c7; }
    .filter-bar { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.85rem; }
    .filter-bar a.active { background: #22c55e; color: white; }
    .checkbox-custom { width: 18px; height: 18px; border-radius: 5px; border: 2px solid #cbd5e1; cursor: pointer; accent-color: #22c55e; }
    .char-count { font-size: 0.75rem; color: var(--text-gray); font-weight: 700; text-align: right; margin-top: 0.25rem; }
    .results-bar { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
    .results-success { background: #f0fdf4; color: #16a34a; }
    .results-warning { background: #fff8e1; color: #f59e0b; }
    .results-error { background: #fef2f2; color: #ef4444; }
    .status-pill { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; }
    .status-authorized { background: #f0fdf4; color: #16a34a; }
    .status-unauthorized { background: #fef2f2; color: #ef4444; }
    .status-pending { background: #fff8e1; color: #f59e0b; }
    .warmup-banner { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
    .warmup-warning { background: #fff8e1; color: #f59e0b; border: 1px solid #fde68a; }
    .warmup-safe { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .template-btn { padding: 0.5rem 1rem; border-radius: 8px; background: #f8fafc; color: #475569; font-size: 0.8rem; font-weight: 700; cursor: pointer; border: 1px solid #e2e8f0; transition: all 0.2s; }
    .template-btn:hover { background: #dcfce7; color: #16a34a; border-color: #22c55e; }
    .checklist { display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem; color: #475569; }
    .checklist-item { display: flex; align-items: center; gap: 0.5rem; }
    .checklist-item i { width: 16px; }
    .check-pass { color: #16a34a; }
    .check-fail { color: #ef4444; }
    .check-warn { color: #f59e0b; }
    .ratio-bar { height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 0.5rem; }
    .ratio-fill { height: 100%; border-radius: 10px; transition: width 0.3s; }
    .campaign-card { background: white; border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow); margin-bottom: 1rem; border-left: 4px solid #22c55e; }
    .campaign-card.status-paused { border-left-color: #f59e0b; }
    .campaign-card.status-cancelled { border-left-color: #ef4444; }
    .campaign-card.status-completed { border-left-color: #3b82f6; }
    .progress-bar { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; background: #22c55e; transition: width 0.3s; }
    .tab-nav { display: flex; gap: 0.5rem; border-bottom: 2px solid #f1f5f9; margin-bottom: 1.5rem; }
    .tab-nav button { padding: 0.75rem 1.25rem; background: none; border: none; border-bottom: 2px solid transparent; font-weight: 800; color: #94a3b8; cursor: pointer; font-size: 0.85rem; }
    .tab-nav button.active { color: #22c55e; border-bottom-color: #22c55e; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .bulk-input { min-height: 120px; font-family: monospace; font-size: 0.85rem; }
    .settings-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; border-radius: 20px; padding: 2rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(5, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 600px) {
        .settings-row { grid-template-columns: 1fr !important; }
        div[style*="grid-template-columns: repeat(3, 1fr)"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        div[style*="grid-template-columns: repeat(5, 1fr)"] { grid-template-columns: 1fr !important; }
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Bulk WhatsApp</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Smart sender with anti-blocking, templates, and bulk number import.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.crm.communications.whatsapp.compare') }}" class="btn-secondary"><i class="fas fa-chart-bar"></i> Compare</a>
            <a href="{{ route('admin.crm.communications.sms') }}" class="btn-secondary"><i class="fas fa-comment-sms"></i> SMS</a>
            <a href="{{ route('admin.crm.communications.whatsapp.qr') }}" class="btn-secondary"><i class="fas fa-qrcode"></i> QR Setup</a>
        </div>
    </div>
</div>

<!-- Warm-up Banner -->
@if($isWarmingUp)
<div class="warmup-banner warmup-warning">
    <i class="fas fa-temperature-low" style="font-size: 1.25rem;"></i>
    <div>
        <strong>Number Warm-up in Progress</strong> — Day {{ $warmupDays }} / 10.
        <span style="font-size: 0.85rem;">Start with 50 msgs/day, increase gradually to 300 by day 10. Use longer delays (15-30s) during warm-up.</span>
    </div>
</div>
@else
<div class="warmup-banner warmup-safe">
    <i class="fas fa-shield-halved" style="font-size: 1.25rem;"></i>
    <div>
        <strong>Number Warm-up Complete</strong> — Day {{ $warmupDays }}+.
        <span style="font-size: 0.85rem;">You can send up to 300/day with 5-15s delays. Always include opt-out and use personalization.</span>
    </div>
</div>
@endif

<!-- Top Stats -->
<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Account</div>
        <div class="status-pill {{ $waState['state'] === 'authorized' ? 'status-authorized' : ($waState['state'] === 'pending' || $waState['state'] === 'starting' ? 'status-pending' : 'status-unauthorized') }}" style="margin: 0 auto;">
            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
            {{ ucfirst($waState['state'] ?? 'unknown') }}
        </div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Response Ratio</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: {{ $responseRatio >= 50 ? '#16a34a' : ($responseRatio >= 30 ? '#f59e0b' : '#ef4444') }};">{{ $responseRatio }}%</div>
        <div class="ratio-bar"><div class="ratio-fill" style="width: {{ min(100, $responseRatio) }}%; background: {{ $responseRatio >= 50 ? '#16a34a' : ($responseRatio >= 30 ? '#f59e0b' : '#ef4444') }};"></div></div>
        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">{{ $incoming }} in / {{ $outgoing }} out</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Queue</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: {{ $queueCount > 0 ? '#f59e0b' : '#16a34a' }};">{{ $queueCount }}</div>
        @if($queueCount > 0)
        <form action="{{ route('admin.crm.communications.whatsapp.clear-queue') }}" method="POST" style="margin-top: 0.5rem;">
            @csrf
            <button type="submit" class="btn-secondary btn-sm"><i class="fas fa-broom"></i> Clear</button>
        </form>
        @endif
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Today Sent</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">{{ $todaySent }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Opted Out</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ef4444;">{{ $optedOutCount }}</div>
    </div>
</div>

<!-- Tabs -->
<div class="tab-nav">
    <button class="active" onclick="switchTab('compose')">Compose</button>
    <button onclick="switchTab('campaigns')">Campaigns ({{ $campaigns->count() }})</button>
    <button onclick="switchTab('templates')">Templates ({{ $templates->count() }})</button>
</div>

<!-- COMPOSE TAB -->
<div id="tab-compose" class="tab-panel active">
    <!-- Pre-send Checklist -->
    <div class="crm-grid" style="margin-bottom: 2rem; padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.75rem;"><i class="fas fa-clipboard-check"></i> Pre-send Checklist</h3>
        <div class="checklist">
            <div class="checklist-item"><i class="fas {{ $waState['state'] === 'authorized' ? 'fa-check-circle check-pass' : 'fa-times-circle check-fail' }}"></i><span>WhatsApp account authorized</span></div>
            <div class="checklist-item"><i class="fas {{ $queueCount === 0 ? 'fa-check-circle check-pass' : 'fa-exclamation-circle check-warn' }}"></i><span>Message queue is empty ({{ $queueCount }} pending)</span></div>
            <div class="checklist-item"><i class="fas {{ $responseRatio >= 50 ? 'fa-check-circle check-pass' : ($responseRatio >= 30 ? 'fa-exclamation-circle check-warn' : 'fa-times-circle check-fail') }}"></i><span>Response ratio is healthy (aim for 50%+)</span></div>
            <div class="checklist-item"><i class="fas {{ $isWarmingUp ? 'fa-exclamation-circle check-warn' : 'fa-check-circle check-pass' }}"></i><span>Number warm-up {{ $isWarmingUp ? 'in progress (day ' . $warmupDays . '/10)' : 'complete' }}</span></div>
        </div>
    </div>

    @if(session('bulk_results'))
    @php $res = session('bulk_results'); @endphp
    <div class="results-bar {{ $res['failed'] === 0 ? 'results-success' : ($res['sent'] > 0 ? 'results-warning' : 'results-error') }}">
        <i class="fas {{ $res['failed'] === 0 ? 'fa-check-circle' : ($res['sent'] > 0 ? 'fa-exclamation-circle' : 'fa-times-circle') }}"></i>
        <div><strong>{{ $res['sent'] }} sent</strong> / {{ $res['total'] }} total @if($res['failed'] > 0) | <strong>{{ $res['failed'] }} failed</strong>@endif</div>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem; align-items: start;">
        <!-- Left: Composer -->
        <div class="crm-grid">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fab fa-whatsapp" style="color: #22c55e;"></i> Compose Campaign</h2>

            <!-- Templates -->
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Templates</label>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
                    @forelse($templates as $tmpl)
                    <button type="button" class="template-btn" onclick="insertTemplate({{ json_encode($tmpl->body) }})">
                        <i class="fas fa-file-lines"></i> {{ $tmpl->name }}
                    </button>
                    @empty
                    <span style="color: #94a3b8; font-size: 0.85rem;">No templates yet. Create one in the Templates tab.</span>
                    @endforelse
                </div>
            </div>

            <form action="{{ route('admin.crm.communications.whatsapp.send') }}" method="POST" id="bulkWaForm">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Campaign Name</label>
                    <input type="text" name="campaign_name" class="form-control" placeholder="e.g. May Promo - Zambia Leads" required maxlength="255">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Message</label>
                    <textarea name="message" id="waMessage" class="form-control" maxlength="4096" placeholder="Type your WhatsApp message here... Use {name} for personalization." required></textarea>
                    <div class="char-count" id="charCount">0 / 4096 characters</div>
                    <div id="shortMsgWarning" style="display: none; font-size: 0.75rem; color: #f59e0b; font-weight: 700; margin-top: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i> Message is quite long. Consider splitting into several short messages for better deliverability.
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.75rem; font-weight: 600; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                        <i class="fas fa-shield-halved" style="color: #22c55e;"></i> <strong>Anti-blocking tip:</strong> Include an opt-out. Example: <em>"Reply STOP to unsubscribe."</em><br>
                        <i class="fas fa-user" style="color: #3b82f6;"></i> Use <code>{name}</code> for personalization — it makes each message unique.<br>
                        <i class="fas fa-comments" style="color: #f59e0b;"></i> Start with a <strong>question</strong> to get replies. Aim for 50%+ response ratio.
                    </div>
                </div>

                <!-- Smart Send Settings -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Smart Send Settings</label>
                    <div class="settings-row">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Delay Min (sec)</label>
                            <input type="number" name="delay_min" class="form-control" value="{{ $isWarmingUp ? 15 : 5 }}" min="3" max="300" required>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Delay Max (sec)</label>
                            <input type="number" name="delay_max" class="form-control" value="{{ $isWarmingUp ? 30 : 15 }}" min="3" max="600" required>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Daily Limit</label>
                            <input type="number" name="daily_limit" class="form-control" value="{{ $isWarmingUp ? 50 : 300 }}" min="50" max="2000" required>
                        </div>
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">
                        Random delay between Min and Max seconds between each message. Daily limit stops the campaign automatically.
                    </div>
                </div>

                <!-- Scheduling -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Scheduling (Optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" min="{{ now()->format('Y-m-d\TH:i') }}">
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">
                        Leave blank to send immediately. Set a future date/time to schedule the campaign.
                    </div>
                </div>

                <!-- A/B Test Toggle -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #8b5cf6; cursor: pointer;">
                        <input type="checkbox" name="is_ab_test" value="1" id="abTestToggle" style="width: 18px; height: 18px; accent-color: #8b5cf6;">
                        <i class="fas fa-flask"></i> Enable A/B Testing (compare two message variants)
                    </label>
                </div>

                <!-- A/B Test Fields (hidden by default) -->
                <div id="abTestFields" style="display: none; margin-bottom: 1.25rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #8b5cf6; text-transform: uppercase; margin-bottom: 0.5rem;">Variant A Message</label>
                            <textarea name="variant_a_message" id="variantA" class="form-control" rows="4" maxlength="4096" placeholder="Message for Variant A..."></textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; margin-bottom: 0.5rem;">Variant B Message</label>
                            <textarea name="variant_b_message" id="variantB" class="form-control" rows="4" maxlength="4096" placeholder="Message for Variant B..."></textarea>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Split % (A)</label>
                            <input type="number" name="split_percent" class="form-control" value="50" min="10" max="90">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Min Replies</label>
                            <input type="number" name="winner_decision_min_replies" class="form-control" value="30" min="5" max="500">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Decision Delay (hrs)</label>
                            <input type="number" name="winner_decision_delay_hours" class="form-control" value="24" min="1" max="72">
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #22c55e; cursor: pointer;">
                            <input type="checkbox" name="auto_send_winner" value="1" checked style="width: 18px; height: 18px; accent-color: #22c55e;">
                            <i class="fas fa-robot"></i> Auto-send winning variant to remaining recipients
                        </label>
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem; padding-left: 1.5rem;">
                            After the winner is declared, remaining pending recipients automatically receive the winning message.
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Alert Threshold (Response Rate %)</label>
                        <input type="number" name="alert_threshold" class="form-control" value="10" min="0" max="100" style="width: 120px;">
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">
                            Set to 0 to disable. You'll get an alert when the campaign hits this response rate.
                        </div>
                    </div>
                </div>

                <!-- Selected Recipients Display -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Selected Recipients</label>
                    <div id="recipientTags" style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 12px; min-height: 40px;">
                        <span style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;">Select contacts or paste numbers below...</span>
                    </div>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-gray); font-weight: 700;"><span id="selectedCount">0</span> selected</div>
                </div>

                <!-- Bulk Number Input -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Bulk Numbers (Paste or Upload CSV)</label>
                    <textarea name="bulk_numbers" id="bulkNumbers" class="form-control bulk-input" placeholder="Paste numbers here...&#10;Format: Name, +26097XXXXXXX&#10;Or: +26097XXXXXXX&#10;One per line. You can also upload a CSV."></textarea>
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
                    <button type="submit" class="btn-whatsapp" id="sendBtn" disabled>
                        <i class="fab fa-whatsapp"></i> Create Campaign & Send
                    </button>
                    <span id="totalRecipientsBadge" style="font-size: 0.85rem; color: var(--text-gray); font-weight: 700;">0 total recipients</span>
                </div>
            </form>
        </div>

        <!-- Right: Contact List -->
        <div class="crm-grid" style="max-height: 800px; overflow-y: auto; overflow-x: auto; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 800;">Contacts <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 700;">(Page {{ $contacts->currentPage() }} of {{ $contacts->lastPage() }} — {{ $contacts->total() }} total)</span></h3>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.crm.communications.export-contacts-csv', ['filter' => request('filter')]) }}" class="btn-secondary btn-sm" style="text-decoration:none;" title="Export current filtered contacts to CSV">
                        <i class="fas fa-file-export"></i> Export
                    </a>
                    <button type="button" class="btn-secondary btn-sm" onclick="selectAll()">Select All</button>
                </div>
            </div>

            <div class="filter-bar" style="margin-bottom: 1rem;">
                <a href="{{ route('admin.crm.communications.whatsapp') }}" class="{{ !request('filter') ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.crm.communications.whatsapp', ['filter' => 'leads']) }}" class="{{ request('filter') === 'leads' ? 'active' : '' }}">Leads</a>
                <a href="{{ route('admin.crm.communications.whatsapp', ['filter' => 'active']) }}" class="{{ request('filter') === 'active' ? 'active' : '' }}">Active</a>
                <a href="{{ route('admin.crm.communications.whatsapp', ['filter' => 'high_value']) }}" class="{{ request('filter') === 'high_value' ? 'active' : '' }}">High Value</a>
            </div>

            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    @forelse($contacts as $contact)
                    <tr class="contact-row" onclick="toggleRow(this)">
                        <td style="width: 30px;">
                            <input type="checkbox" class="checkbox-custom contact-check" value="{{ $contact->phone }}" data-name="{{ $contact->name }}" onclick="event.stopPropagation()">
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
                    <tr><td colspan="3" style="text-align: center; padding: 2rem 0; color: var(--text-gray);">No contacts with phone numbers found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($contacts->hasPages())
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                @if($contacts->onFirstPage())
                    <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">« Previous</span>
                @else
                    <a href="{{ $contacts->previousPageUrl() }}{{ request('filter') ? '&filter=' . request('filter') : '' }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">« Previous</a>
                @endif

                @foreach($contacts->getUrlRange(max(1, $contacts->currentPage() - 2), min($contacts->lastPage(), $contacts->currentPage() + 2)) as $page => $url)
                    @if($page === $contacts->currentPage())
                        <span style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #22c55e; color: white; font-size: 0.8rem; font-weight: 700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}{{ request('filter') ? '&filter=' . request('filter') : '' }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">{{ $page }}</a>
                    @endif
                @endforeach

                @if($contacts->hasMorePages())
                    <a href="{{ $contacts->nextPageUrl() }}{{ request('filter') ? '&filter=' . request('filter') : '' }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">Next »</a>
                @else
                    <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">Next »</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- CAMPAIGNS TAB -->
<div id="tab-campaigns" class="tab-panel">
    <div class="crm-grid">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-paper-plane" style="color: #22c55e;"></i> Recent Campaigns</h2>

        @forelse($campaigns as $camp)
        <div class="campaign-card status-{{ $camp->status }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                    <div style="font-weight: 800; color: var(--text-dark); font-size: 0.95rem;">{{ $camp->name }}</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem;">
                        {{ $camp->created_at->format('M d, Y H:i') }} &middot;
                        <span style="text-transform: uppercase; font-weight: 800;">{{ $camp->status }}</span> &middot;
                        {{ $camp->delay_min }}–{{ $camp->delay_max }}s delay &middot; {{ $camp->daily_limit }}/day limit
                    </div>
                </div>
                <div style="display: flex; gap: 0.4rem;">
                    @if(in_array($camp->status, ['queued', 'sending']))
                    <form action="{{ route('admin.crm.communications.whatsapp.campaigns.pause', $camp) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn-secondary btn-sm btn-warning"><i class="fas fa-pause"></i> Pause</button></form>
                    @endif
                    @if($camp->status === 'paused')
                    <form action="{{ route('admin.crm.communications.whatsapp.campaigns.resume', $camp) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn-secondary btn-sm" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-play"></i> Resume</button></form>
                    @endif
                    @if(!in_array($camp->status, ['completed', 'cancelled']))
                    <form action="{{ route('admin.crm.communications.whatsapp.campaigns.cancel', $camp) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this campaign?')">@csrf<button type="submit" class="btn-secondary btn-sm btn-danger"><i class="fas fa-times"></i> Cancel</button></form>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 0.75rem; font-size: 0.8rem;">
                <div><span style="color: #94a3b8; font-weight: 700;">Total:</span> <strong style="color: var(--text-dark);">{{ $camp->total_recipients }}</strong></div>
                <div><span style="color: #94a3b8; font-weight: 700;">Sent:</span> <strong style="color: #16a34a;">{{ $camp->sent_count }}</strong></div>
                <div><span style="color: #94a3b8; font-weight: 700;">Failed:</span> <strong style="color: #ef4444;">{{ $camp->failed_count }}</strong></div>
                <div><span style="color: #94a3b8; font-weight: 700;">Pending:</span> <strong style="color: #3b82f6;">{{ $camp->pending_r ?? ($camp->total_recipients - $camp->sent_count - $camp->failed_count - $camp->opted_out_count) }}</strong></div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $camp->progressPercent() }}%;"></div>
            </div>
            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem; text-align: right;">{{ $camp->progressPercent() }}% complete</div>
        </div>
        @empty
        <div style="text-align: center; padding: 3rem 0; color: #94a3b8;">
            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
            No campaigns yet. Create one in the Compose tab.
        </div>
        @endforelse
    </div>
</div>

<!-- TEMPLATES TAB -->
<div id="tab-templates" class="tab-panel">
    <div class="crm-grid">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800;"><i class="fas fa-file-lines" style="color: #3b82f6;"></i> Message Templates</h2>
            <button type="button" class="btn-whatsapp" onclick="openTemplateModal()" style="padding: 0.5rem 1rem; font-size: 0.8rem;"><i class="fas fa-plus"></i> New Template</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
            @forelse($templates as $tmpl)
            <div style="background: #f8fafc; border-radius: 12px; padding: 1rem; border: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark);">{{ $tmpl->name }}</div>
                        <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">{{ $tmpl->category }} &middot; {{ $tmpl->channel }}</div>
                    </div>
                    <div style="display: flex; gap: 0.3rem;">
                        <button type="button" class="btn-secondary btn-sm" onclick="editTemplate({{ $tmpl->id }}, {{ json_encode($tmpl->name) }}, {{ json_encode($tmpl->body) }}, {{ json_encode($tmpl->channel) }}, {{ json_encode($tmpl->category) }})"><i class="fas fa-pen"></i></button>
                        <form action="{{ route('admin.crm.communications.templates.destroy', $tmpl) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this template?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <div style="font-size: 0.8rem; color: #475569; background: white; padding: 0.75rem; border-radius: 8px; border: 1px solid #f1f5f9; white-space: pre-wrap; max-height: 120px; overflow-y: auto;">{{ $tmpl->body }}</div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #94a3b8;">No templates yet. Click "New Template" to create one.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Template Modal -->
<div id="templateModal" class="modal-overlay">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800;" id="modalTitle">New Template</h3>
            <button type="button" onclick="closeTemplateModal()" style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:#94a3b8;">&times;</button>
        </div>
        <form id="templateForm" action="{{ route('admin.crm.communications.templates.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="templateMethod" value="POST">
            <input type="hidden" id="templateId" name="template_id">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.4rem;">Name</label>
                <input type="text" name="name" id="tmplName" class="form-control" required maxlength="255">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.4rem;">Channel</label>
                    <select name="channel" id="tmplChannel" class="form-control" required>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.4rem;">Category</label>
                    <select name="category" id="tmplCategory" class="form-control" required>
                        <option value="general">General</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="promo">Promo</option>
                        <option value="reminder">Reminder</option>
                        <option value="invoice">Invoice</option>
                        <option value="custom_clearance">Customs</option>
                        <option value="onboarding">Onboarding</option>
                        <option value="event">Event</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.4rem;">Body</label>
                <textarea name="body" id="tmplBody" class="form-control" rows="5" maxlength="4096" required placeholder="Use {name} for personalization..."></textarea>
            </div>
            <button type="submit" class="btn-whatsapp" style="width: 100%; justify-content: center;">Save Template</button>
        </form>
    </div>
</div>

<script>
    const checkboxes = document.querySelectorAll('.contact-check');
    const recipientTags = document.getElementById('recipientTags');
    const sendBtn = document.getElementById('sendBtn');
    const selectedCount = document.getElementById('selectedCount');
    const totalRecipientsBadge = document.getElementById('totalRecipientsBadge');
    const waMessage = document.getElementById('waMessage');
    const charCount = document.getElementById('charCount');
    const shortMsgWarning = document.getElementById('shortMsgWarning');
    const bulkNumbers = document.getElementById('bulkNumbers');
    let selectedPhones = [];
    let bulkPhones = [];

    function updateTotals() {
        const total = new Set([...selectedPhones, ...bulkPhones]).size;
        totalRecipientsBadge.textContent = total + ' total recipients';
        sendBtn.disabled = total === 0 || waMessage.value.trim().length === 0;
    }

    function updateRecipientTags() {
        recipientTags.innerHTML = '';
        const all = new Set([...selectedPhones, ...bulkPhones]);
        if (all.size === 0) {
            recipientTags.innerHTML = '<span style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;">Select contacts or paste numbers below...</span>';
        } else {
            Array.from(all).slice(0, 20).forEach(phone => {
                const tag = document.createElement('span');
                tag.style.cssText = 'background: #dcfce7; color: #16a34a; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700;';
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
        selectedCount.textContent = all.size;
        updateTotals();
        updateFormInputs();
    }

    function updateFormInputs() {
        document.querySelectorAll('input[name="recipients[]"]').forEach(i => i.remove());
        const all = [...new Set([...selectedPhones, ...bulkPhones])];
        all.forEach(phone => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'recipients[]';
            inp.value = phone;
            document.getElementById('bulkWaForm').appendChild(inp);
        });
    }

    function toggleRow(row) {
        const cb = row.querySelector('.contact-check');
        cb.checked = !cb.checked;
        const phone = cb.value;
        if (cb.checked) {
            if (!selectedPhones.includes(phone)) selectedPhones.push(phone);
        } else {
            selectedPhones = selectedPhones.filter(p => p !== phone);
        }
        updateRecipientTags();
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const phone = this.value;
            if (this.checked) {
                if (!selectedPhones.includes(phone)) selectedPhones.push(phone);
            } else {
                selectedPhones = selectedPhones.filter(p => p !== phone);
            }
            updateRecipientTags();
        });
    });

    function selectAll() {
        const allPhones = Array.from(checkboxes).map(cb => cb.value);
        const allChecked = selectedPhones.length === allPhones.length && allPhones.length > 0;
        checkboxes.forEach(cb => cb.checked = !allChecked);
        selectedPhones = allChecked ? [] : [...allPhones];
        updateRecipientTags();
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
        updateRecipientTags();
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
        status.textContent = 'Uploading...';

        const formData = new FormData();
        formData.append('csv', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('admin.crm.communications.whatsapp.upload-recipients') }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            status.textContent = data.count + ' numbers loaded from CSV.';
            const existing = bulkNumbers.value.trim();
            const newLines = data.recipients.map(r => (r.name ? r.name + ', ' : '') + r.phone).join('\n');
            bulkNumbers.value = existing ? existing + '\n' + newLines : newLines;
            bulkNumbers.dispatchEvent(new Event('input'));
        })
        .catch(err => {
            status.textContent = 'Error: ' + err.message;
        });
    }

    // Message composer
    waMessage.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = len + ' / 4096 characters';
        shortMsgWarning.style.display = len > 800 ? 'block' : 'none';
        updateTotals();
    });

    function insertTemplate(body) {
        waMessage.value = body;
        waMessage.dispatchEvent(new Event('input'));
    }

    // Tabs
    function switchTab(id) {
        document.querySelectorAll('.tab-nav button').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('tab-' + id).classList.add('active');
    }

    // Template Modal
    function openTemplateModal() {
        document.getElementById('templateModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'New Template';
        document.getElementById('templateForm').action = '{{ route('admin.crm.communications.templates.store') }}';
        document.getElementById('templateMethod').value = 'POST';
        document.getElementById('tmplName').value = '';
        document.getElementById('tmplBody').value = '';
        document.getElementById('tmplChannel').value = 'whatsapp';
        document.getElementById('tmplCategory').value = 'general';
    }

    function editTemplate(id, name, body, channel, category) {
        document.getElementById('templateModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Template';
        document.getElementById('templateForm').action = '{{ url('admin/crm/communications/templates') }}/' + id;
        document.getElementById('templateMethod').value = 'PUT';
        document.getElementById('tmplName').value = name;
        document.getElementById('tmplBody').value = body;
        document.getElementById('tmplChannel').value = channel;
        document.getElementById('tmplCategory').value = category;
    }

    function closeTemplateModal() {
        document.getElementById('templateModal').classList.remove('active');
    }

    // A/B Test toggle
    document.getElementById('abTestToggle').addEventListener('change', function() {
        const fields = document.getElementById('abTestFields');
        fields.style.display = this.checked ? 'block' : 'none';
        // When A/B is enabled, the main message textarea is not required
        waMessage.required = !this.checked;
        document.getElementById('variantA').required = this.checked;
        document.getElementById('variantB').required = this.checked;
        updateTotals();
    });

    // Close modal on overlay click
    document.getElementById('templateModal').addEventListener('click', function(e) {
        if (e.target === this) closeTemplateModal();
    });
</script>
@endsection
