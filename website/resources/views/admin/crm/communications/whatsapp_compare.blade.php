@extends('layouts.dashboard')

@section('title', 'Compare Campaigns - WhatsApp - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .compare-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); border-top: 4px solid #22c55e; }
    .compare-card.ab-test { border-top-color: #8b5cf6; }
    .compare-card.scheduled { border-top-color: #3b82f6; }
    .stat-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f8fafc; font-size: 0.85rem; }
    .stat-row:last-child { border-bottom: none; }
    .stat-label { color: #94a3b8; font-weight: 700; }
    .stat-value { color: var(--text-dark); font-weight: 800; }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-secondary:hover { background: #e2e8f0; }
    .form-control { padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.9rem; outline: none; background: white; width: 100%; }
    .winner-badge { background: #f0fdf4; color: #16a34a; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Compare Campaigns</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Side-by-side performance comparison of your WhatsApp campaigns.</p>
        </div>
        <a href="{{ route('admin.crm.communications.whatsapp') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<!-- Campaign Selector -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;"><i class="fas fa-filter" style="color: #3b82f6;"></i> Select Campaigns to Compare</h3>
    <form action="{{ route('admin.crm.communications.whatsapp.compare') }}" method="GET">
        <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: end;">
            <div>
                <select name="campaigns[]" class="form-control" multiple size="6" style="min-height: 150px;">
                    @foreach($allCampaigns as $c)
                    <option value="{{ $c->id }}" {{ in_array($c->id, request('campaigns', [])) ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->created_at->format('M d') }})
                    </option>
                    @endforeach
                </select>
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">Hold Ctrl/Cmd to select multiple campaigns (max 4 recommended).</div>
            </div>
            <div>
                <button type="submit" class="btn-secondary" style="background: #22c55e; color: white; height: fit-content;">
                    <i class="fas fa-chart-bar"></i> Compare
                </button>
            </div>
        </div>
    </form>
</div>

@if($campaigns->count() > 0)
<!-- Comparison Table -->
<div class="crm-grid" style="margin-bottom: 2rem; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; min-width: 700px;">
        <thead>
            <tr style="border-bottom: 2px solid #f1f5f9;">
                <th style="text-align: left; padding: 0.75rem; font-weight: 800; color: var(--text-gray);">Metric</th>
                @foreach($campaigns as $c)
                <th style="text-align: center; padding: 0.75rem; font-weight: 800; color: var(--text-dark);">
                    {{ $c->name }}
                    @if($c->is_ab_test)
                    <span style="display: block; font-size: 0.65rem; color: #8b5cf6; font-weight: 700;">A/B TEST</span>
                    @endif
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Status</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center;">
                    <span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $c->status === 'completed' ? '#f0fdf4' : ($c->status === 'sending' ? '#dcfce7' : ($c->status === 'paused' ? '#fff8e1' : ($c->status === 'cancelled' ? '#fef2f2' : '#f1f5f9'))) }}; color: {{ $c->status === 'completed' ? '#16a34a' : ($c->status === 'sending' ? '#16a34a' : ($c->status === 'paused' ? '#f59e0b' : ($c->status === 'cancelled' ? '#ef4444' : '#475569'))) }};">{{ ucfirst($c->status) }}</span>
                </td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Total Recipients</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800;">{{ $c->total_r }}</td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Sent</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800; color: #16a34a;">{{ $c->sent_r }}</td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Failed</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800; color: #ef4444;">{{ $c->failed_r }}</td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Delivery Rate</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800;">
                    @if($c->total_r > 0)
                        {{ round(($c->sent_r / $c->total_r) * 100, 1) }}%
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc; background: #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #3b82f6;">Replies</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800; color: #3b82f6;">{{ $c->reply_r }}</td>
                @endforeach
            </tr>
            <tr style="border-bottom: 2px solid #f1f5f9; background: #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #3b82f6;">Response Rate</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; font-weight: 800; color: #3b82f6; font-size: 1.1rem;">
                    @if($c->sent_r > 0)
                        {{ round(($c->reply_r / $c->sent_r) * 100, 1) }}%
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Delay Range</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center;">{{ $c->delay_min }}–{{ $c->delay_max }}s</td>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid #f8fafc;">
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Daily Limit</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center;">{{ $c->daily_limit }}</td>
                @endforeach
            </tr>
            <tr>
                <td style="padding: 0.75rem; font-weight: 700; color: #94a3b8;">Created</td>
                @foreach($campaigns as $c)
                <td style="padding: 0.75rem; text-align: center; color: #94a3b8;">{{ $c->created_at->format('M d, Y') }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

<!-- Visual Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
    @foreach($campaigns as $c)
    @php
        $responseRate = $c->sent_r > 0 ? round(($c->reply_r / $c->sent_r) * 100, 1) : 0;
        $deliveryRate = $c->total_r > 0 ? round(($c->sent_r / $c->total_r) * 100, 1) : 0;
    @endphp
    <div class="compare-card {{ $c->is_ab_test ? 'ab-test' : '' }} {{ $c->scheduled_at ? 'scheduled' : '' }}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-dark);">{{ $c->name }}</div>
            <a href="{{ route('admin.crm.communications.whatsapp.campaigns.show', $c) }}" class="btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
        </div>

        <div class="stat-row">
            <span class="stat-label">Response Rate</span>
            <span class="stat-value" style="color: #3b82f6; font-size: 1.1rem;">{{ $responseRate }}%</span>
        </div>
        <div style="height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
            <div style="height: 100%; width: {{ min(100, $responseRate * 3) }}%; background: #3b82f6; border-radius: 10px;"></div>
        </div>

        <div class="stat-row">
            <span class="stat-label">Delivery Rate</span>
            <span class="stat-value">{{ $deliveryRate }}%</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Sent / Total</span>
            <span class="stat-value">{{ $c->sent_r }} / {{ $c->total_r }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Replies</span>
            <span class="stat-value" style="color: #16a34a;">{{ $c->reply_r }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Failed</span>
            <span class="stat-value" style="color: #ef4444;">{{ $c->failed_r }}</span>
        </div>
        @if($c->is_ab_test && $c->winner_variant)
        <div style="margin-top: 1rem; padding: 0.5rem; background: #f0fdf4; border-radius: 8px; text-align: center;">
            <span class="winner-badge"><i class="fas fa-trophy"></i> Winner: Variant {{ strtoupper($c->winner_variant) }}</span>
        </div>
        @endif
    </div>
    @endforeach
</div>
@else
<div class="crm-grid" style="text-align: center; padding: 3rem; color: #94a3b8;">
    <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
    <p style="font-weight: 700; font-size: 1rem;">Select campaigns above to compare their performance.</p>
</div>
@endif
@endsection
