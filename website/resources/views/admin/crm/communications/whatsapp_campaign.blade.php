@extends('layouts.dashboard')

@section('title', 'Campaign Detail - WhatsApp - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .progress-bar { height: 10px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; background: #22c55e; transition: width 0.3s; }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }
    .btn-danger { background: #fef2f2; color: #ef4444; }
    .btn-danger:hover { background: #fee2e2; }
    .btn-warning { background: #fff8e1; color: #f59e0b; }
    .btn-warning:hover { background: #fef3c7; }
    .ab-card { background: #f8fafc; border-radius: 16px; padding: 1.5rem; border: 2px solid #e2e8f0; }
    .ab-card.winner { border-color: #22c55e; background: #f0fdf4; }
    .ab-bar { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 0.5rem; }
    .ab-fill { height: 100%; border-radius: 10px; }
    .status-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">{{ $campaign->name }}</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">
                <span class="status-badge" style="background: {{ $campaign->status === 'completed' ? '#f0fdf4' : ($campaign->status === 'sending' ? '#dcfce7' : ($campaign->status === 'paused' ? '#fff8e1' : ($campaign->status === 'cancelled' ? '#fef2f2' : '#f1f5f9'))) }}; color: {{ $campaign->status === 'completed' ? '#16a34a' : ($campaign->status === 'sending' ? '#16a34a' : ($campaign->status === 'paused' ? '#f59e0b' : ($campaign->status === 'cancelled' ? '#ef4444' : '#475569'))) }};">
                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i> {{ ucfirst($campaign->status) }}
                </span>
                &middot; Created {{ $campaign->created_at->format('M d, Y H:i') }}
                @if($campaign->scheduled_at)
                &middot; Scheduled for {{ $campaign->scheduled_at->format('M d, Y H:i') }}
                @endif
                @if($campaign->is_ab_test)
                &middot; <span style="color: #3b82f6; font-weight: 800;">A/B Test</span>
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.crm.communications.whatsapp') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            @if(in_array($campaign->status, ['queued', 'sending']))
            <form action="{{ route('admin.crm.communications.whatsapp.campaigns.pause', $campaign) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn-secondary btn-warning"><i class="fas fa-pause"></i> Pause</button></form>
            @endif
            @if($campaign->status === 'paused')
            <form action="{{ route('admin.crm.communications.whatsapp.campaigns.resume', $campaign) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn-secondary" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-play"></i> Resume</button></form>
            @endif
            @if(!in_array($campaign->status, ['completed', 'cancelled']))
            <form action="{{ route('admin.crm.communications.whatsapp.campaigns.cancel', $campaign) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this campaign?')">@csrf<button type="submit" class="btn-secondary btn-danger"><i class="fas fa-times"></i> Cancel</button></form>
            @endif
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Total</div>
        <div style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark);">{{ $campaign->total_recipients }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Sent</div>
        <div style="font-size: 1.75rem; font-weight: 900; color: #16a34a;">{{ $campaign->sent_count }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Failed</div>
        <div style="font-size: 1.75rem; font-weight: 900; color: #ef4444;">{{ $campaign->failed_count }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Pending</div>
        <div style="font-size: 1.75rem; font-weight: 900; color: #3b82f6;">{{ $campaign->pending_count }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Progress</div>
        <div style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark);">{{ $campaign->progressPercent() }}%</div>
    </div>
</div>

<div style="margin-bottom: 2rem;">
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ $campaign->progressPercent() }}%;"></div>
    </div>
</div>

<!-- A/B Test Results -->
@if($campaign->is_ab_test && $abStats)
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-flask" style="color: #8b5cf6;"></i> A/B Test Results</h2>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Variant A -->
        <div class="ab-card {{ $abStats['winner'] === 'A' ? 'winner' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="font-weight: 800; font-size: 1rem; color: var(--text-dark);">Variant A</div>
                @if($abStats['winner'] === 'A')
                <span style="background: #22c55e; color: white; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800;"><i class="fas fa-trophy"></i> WINNER</span>
                @endif
            </div>
            <div style="background: white; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 1rem; font-size: 0.85rem; color: #475569; white-space: pre-wrap;">{{ $campaign->variant_a_message }}</div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <div><div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">SENT</div><div style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);">{{ $abStats['a_sent'] }}</div></div>
                <div><div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">REPLIES</div><div style="font-size: 1.25rem; font-weight: 800; color: #16a34a;">{{ $abStats['a_replies'] }}</div></div>
            </div>
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; color: #475569;">
                    <span>Response Rate</span>
                    <span>{{ $abStats['a_rate'] }}%</span>
                </div>
                <div class="ab-bar">
                    <div class="ab-fill" style="width: {{ min(100, $abStats['a_rate'] * 2) }}%; background: #8b5cf6;"></div>
                </div>
            </div>
        </div>

        <!-- Variant B -->
        <div class="ab-card {{ $abStats['winner'] === 'B' ? 'winner' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="font-weight: 800; font-size: 1rem; color: var(--text-dark);">Variant B</div>
                @if($abStats['winner'] === 'B')
                <span style="background: #22c55e; color: white; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800;"><i class="fas fa-trophy"></i> WINNER</span>
                @endif
            </div>
            <div style="background: white; padding: 1rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 1rem; font-size: 0.85rem; color: #475569; white-space: pre-wrap;">{{ $campaign->variant_b_message }}</div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <div><div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">SENT</div><div style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);">{{ $abStats['b_sent'] }}</div></div>
                <div><div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">REPLIES</div><div style="font-size: 1.25rem; font-weight: 800; color: #16a34a;">{{ $abStats['b_replies'] }}</div></div>
            </div>
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; color: #475569;">
                    <span>Response Rate</span>
                    <span>{{ $abStats['b_rate'] }}%</span>
                </div>
                <div class="ab-bar">
                    <div class="ab-fill" style="width: {{ min(100, $abStats['b_rate'] * 2) }}%; background: #f59e0b;"></div>
                </div>
            </div>
        </div>
    </div>

    @if($abStats['winner'])
    <div style="margin-top: 1.5rem; padding: 1rem; background: #f0fdf4; border-radius: 12px; text-align: center; font-weight: 700; color: #16a34a;">
        <i class="fas fa-trophy"></i> Variant {{ $abStats['winner'] }} is winning with a {{ $abStats['winner'] === 'A' ? $abStats['a_rate'] : $abStats['b_rate'] }}% response rate
    </div>
    @else
    <div style="margin-top: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 12px; text-align: center; font-weight: 700; color: #94a3b8;">
        <i class="fas fa-chart-line"></i> A/B test in progress — more data needed to declare a winner
    </div>
    @endif
</div>
@endif

<!-- Alerts -->
@if($alerts && $alerts->count() > 0)
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-bell" style="color: #f59e0b;"></i> Alerts ({{ $alerts->count() }})</h2>
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        @foreach($alerts as $alert)
        <div style="padding: 1rem; border-radius: 12px; background: {{ $alert->type === 'threshold_exceeded' ? '#f0fdf4' : ($alert->type === 'winner_declared' ? '#e0e7ff' : '#fff8e1') }}; border-left: 4px solid {{ $alert->type === 'threshold_exceeded' ? '#16a34a' : ($alert->type === 'winner_declared' ? '#6366f1' : '#f59e0b') }};">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--text-dark);">{{ $alert->message }}</span>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">{{ $alert->created_at->format('M d, Y H:i') }}</div>
                </div>
                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; background: white; color: {{ $alert->type === 'threshold_exceeded' ? '#16a34a' : ($alert->type === 'winner_declared' ? '#6366f1' : '#f59e0b') }};">
                    {{ str_replace('_', ' ', $alert->type) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Auto-send Winner Status -->
@if($campaign->is_ab_test && $campaign->winner_variant)
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-robot" style="color: #22c55e;"></i> Auto-Send Winner Status</h2>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Winner Declared</div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #16a34a;">{{ $campaign->winner_declared_at->format('M d, Y H:i') }}</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Winning Variant</div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #8b5cf6;">Variant {{ strtoupper($campaign->winner_variant) }}</div>
        </div>
        <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Auto-Send Enabled</div>
            <div style="font-size: 1.25rem; font-weight: 900; color: {{ $campaign->auto_send_winner ? '#16a34a' : '#ef4444' }};">{{ $campaign->auto_send_winner ? 'Yes' : 'No' }}</div>
        </div>
    </div>
</div>
@endif

<!-- Recipients Table -->
<div class="crm-grid">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><i class="fas fa-users" style="color: #3b82f6;"></i> Recipients</h2>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="border-bottom: 2px solid #f1f5f9;">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Phone</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Name</th>
                    @if($campaign->is_ab_test)
                    <th style="text-align: center; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Variant</th>
                    @endif
                    <th style="text-align: center; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Status</th>
                    <th style="text-align: center; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Replied</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaign->recipients()->orderBy('id')->take(100)->get() as $r)
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 0.75rem; font-weight: 600;">{{ $r->phone }}</td>
                    <td style="padding: 0.75rem;">{{ $r->name ?? '-' }}</td>
                    @if($campaign->is_ab_test)
                    <td style="padding: 0.75rem; text-align: center;">
                        <span style="font-weight: 800; color: {{ $r->variant === 'a' ? '#8b5cf6' : '#f59e0b' }};">{{ strtoupper($r->variant ?? '-') }}</span>
                    </td>
                    @endif
                    <td style="padding: 0.75rem; text-align: center;">
                        <span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $r->status === 'sent' ? '#f0fdf4' : ($r->status === 'pending' ? '#fff8e1' : ($r->status === 'failed' ? '#fef2f2' : ($r->status === 'opted_out' ? '#fef2f2' : '#f1f5f9'))) }}; color: {{ $r->status === 'sent' ? '#16a34a' : ($r->status === 'pending' ? '#f59e0b' : ($r->status === 'failed' ? '#ef4444' : ($r->status === 'opted_out' ? '#ef4444' : '#475569'))) }};">{{ ucfirst($r->status) }}</span>
                    </td>
                    <td style="padding: 0.75rem; text-align: center;">
                        @if($r->replied_at)
                        <i class="fas fa-check-circle" style="color: #16a34a;"></i> {{ $r->replied_at->format('M d H:i') }}
                        @else
                        <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="padding: 0.75rem; color: #94a3b8;">{{ $r->sent_at ? $r->sent_at->format('M d, Y H:i') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">No recipients yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
