@extends('layouts.dashboard')

@section('title', 'System Reports & Analytics - Forus Freight')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .report-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .report-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .report-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--text-dark);
    }

    .chart-container {
        background: white;
        border-radius: 30px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2.5rem;
        position: relative;
        min-width: 0;
    }

    .log-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }

    .log-row {
        background: #f8fafc;
        border-radius: 12px;
    }

    .log-row td {
        padding: 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
    }

    .log-row td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .log-row td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .type-pill {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    @media (max-width: 900px) {
        .report-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .report-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Enterprise Analytics</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">High-level business intelligence and system performance metrics.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('admin.reports.export', ['type' => 'shipments']) }}" style="background: white; color: var(--text-dark); padding: 0.8rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 800; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
            <i class="fas fa-file-csv" style="color: #10b981;"></i> Shipments CSV
        </a>
        <a href="{{ route('admin.reports.export', ['type' => 'clients']) }}" style="background: white; color: var(--text-dark); padding: 0.8rem 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 800; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
            <i class="fas fa-file-csv" style="color: #6366f1;"></i> Client CRM CSV
        </a>
    </div>
</div>

<div class="report-grid">
    <div class="report-card">
        <div class="report-label"><i class="fas fa-sack-dollar" style="color: #10b981;"></i> Lifetime Revenue</div>
        <div class="report-value">{{ number_format($totalRevenue, 2) }} <span style="font-size: 0.8rem; opacity: 0.5;">ZMW</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-calendar-check" style="color: #3b82f6;"></i> Monthly Revenue</div>
        <div class="report-value">{{ number_format($monthlyRevenue, 2) }} <span style="font-size: 0.8rem; opacity: 0.5;">ZMW</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-users" style="color: #6366f1;"></i> Total Clients</div>
        <div class="report-value">{{ $totalClients }} <span style="font-size: 0.8rem; opacity: 0.5;">ACCOUNTS</span></div>
    </div>
    <div class="report-card">
        <div class="report-label"><i class="fas fa-user-plus" style="color: #f59e0b;"></i> New Leads</div>
        <div class="report-value">{{ $leadsCount }} <span style="font-size: 0.8rem; opacity: 0.5;">PIPELINE</span></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
    <div class="chart-container">
        <h3 style="font-weight: 800; margin-bottom: 2rem;">Shipment Distribution</h3>
        <canvas id="shipmentChart" style="max-height: 300px;"></canvas>
    </div>
    <div class="chart-container">
        <h3 style="font-weight: 800; margin-bottom: 2rem;">Communication Channels</h3>
        <canvas id="commChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<div class="chart-container">
    <h3 style="font-weight: 800; margin-bottom: 2rem;">Global Communication Activity (Recent)</h3>
    <div style="overflow-x: auto;">
    <table class="log-table">
        <tbody>
            @foreach($recentLogs as $log)
                <tr class="log-row">
                    <td style="width: 50px;">
                        @if($log->type === 'email')
                            <span class="type-pill" style="background: #eff6ff; color: #3b82f6;">Email</span>
                        @elseif($log->type === 'sms')
                            <span class="type-pill" style="background: #fffbeb; color: #f59e0b;">SMS</span>
                        @elseif($log->type === 'whatsapp')
                            <span class="type-pill" style="background: #f0fdf4; color: #22c55e;">WA</span>
                        @else
                            <span class="type-pill" style="background: #f1f5f9; color: #64748b;">CRM</span>
                        @endif
                    </td>
                    <td style="font-weight: 800;">{{ $log->user->name }}</td>
                    <td style="color: #64748b;">"{{ Str::limit($log->message, 80) }}"</td>
                    <td style="text-align: right; font-size: 0.75rem; color: #94a3b8;">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<script>
    // Shipment Chart
    const shipCtx = document.getElementById('shipmentChart').getContext('2d');
    new Chart(shipCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($shipmentStats->keys()) !!},
            datasets: [{
                data: {!! json_encode($shipmentStats->values()) !!},
                backgroundColor: ['#4caf50', '#3b82f6', '#f59e0b', '#6366f1', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Comm Chart
    const commCtx = document.getElementById('commChart').getContext('2d');
    new Chart(commCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($commStats->keys()) !!},
            datasets: [{
                label: 'Messages Sent',
                data: {!! json_encode($commStats->values()) !!},
                backgroundColor: '#6366f1',
                borderRadius: 10
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
