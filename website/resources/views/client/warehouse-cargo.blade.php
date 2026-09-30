@extends('layouts.dashboard')

@section('title', 'My Warehouse Cargo - Forus Freight')

@section('styles')
<style>
    .cargo-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); margin-bottom: 2rem; overflow-x: auto; }
    .cargo-row { transition: all 0.2s; border-bottom: 1px solid #f1f5f9; padding: 1.5rem 0; }
    .cargo-row:last-child { border-bottom: none; }
    .cargo-row:hover { background: #f8fafc; padding-left: 1rem; padding-right: 1rem; border-radius: 8px; }
    .cargo-header { display: grid; grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr; gap: 1rem; padding: 1.5rem 0; font-weight: 800; color: var(--text-gray); font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; min-width: 700px; }
    .cargo-item { display: grid; grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr; gap: 1rem; align-items: center; padding: 1.5rem 0; min-width: 700px; }
    .cargo-name { display: flex; flex-direction: column; }
    .cargo-name-en { font-weight: 800; color: var(--text-dark); font-size: 0.95rem; }
    .cargo-name-cn { font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem; }
    .stat-badge { display: inline-block; padding: 0.4rem 0.8rem; background: #f1f5f9; color: #475569; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-align: center; }
    .status-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; }
    .status-warehouse { background: #f0fdf4; color: #16a34a; }
    .filter-bar { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
    .filter-bar a { padding: 0.6rem 1rem; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 800; text-decoration: none; font-size: 0.85rem; }
    .filter-bar a.active { background: var(--primary-green); color: white; }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow); text-align: center; }
    .stat-value { font-size: 1.5rem; font-weight: 900; color: var(--text-dark); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-top: 0.5rem; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
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
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">My Warehouse Cargo</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">View all your stored cargo and inventory in our warehouse.</p>
        </div>
    </div>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-value" style="color: var(--primary-green);">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Entries</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #3b82f6;">{{ $stats['cartons'] }}</div>
        <div class="stat-label">Total Cartons</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['weight'] }}</div>
        <div class="stat-label">Total Weight (KG)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #8b5cf6;">{{ $stats['volume'] }}</div>
        <div class="stat-label">Total Volume (m³)</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <a href="{{ route('client.warehouse.cargo') }}" class="{{ !request('status') ? 'active' : '' }}">All Cargo</a>
    <a href="{{ route('client.warehouse.cargo', ['status' => 'in_warehouse']) }}" class="{{ request('status') === 'in_warehouse' ? 'active' : '' }}">In Warehouse</a>
    <a href="{{ route('client.warehouse.cargo', ['status' => 'shipped']) }}" class="{{ request('status') === 'shipped' ? 'active' : '' }}">Shipped</a>
</div>

<!-- Cargo List -->
<div class="cargo-grid">
    @if($cargos->count() > 0)
        <div class="cargo-header">
            <div>Cargo Name</div>
            <div>Entry Info</div>
            <div>Cartons</div>
            <div>Weight</div>
            <div>Volume</div>
            <div>Status</div>
        </div>

        @foreach($cargos as $cargo)
        <div class="cargo-row">
            <div class="cargo-name">
                <span class="cargo-name-en">{{ $cargo->cargo_name_english }}</span>
                @if($cargo->cargo_name_chinese)
                <span class="cargo-name-cn">{{ $cargo->cargo_name_chinese }}</span>
                @endif
            </div>
            <div>
                <div style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">{{ $cargo->warehouse_entry_number }}</div>
                <div style="font-size: 0.75rem; color: var(--text-gray);">{{ $cargo->entry_date->format('d/m/Y') }}</div>
                @if($cargo->receiver_name)
                <div style="font-size: 0.75rem; color: #94a3b8;">Receiver: {{ $cargo->receiver_name }}</div>
                @endif
            </div>
            <div>
                <span class="stat-badge">{{ $cargo->cartons }} boxes</span>
            </div>
            <div>
                <span class="stat-badge">{{ $cargo->gross_weight ?? 'N/A' }} KG</span>
            </div>
            <div>
                <span class="stat-badge">{{ $cargo->volume ?? 'N/A' }} m³</span>
            </div>
            <div>
                <span class="status-badge status-warehouse">
                    <i class="fas fa-check-circle"></i> {{ $cargo->status }}
                </span>
            </div>
        </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 3rem 2rem; color: var(--text-gray);">
            <i class="fas fa-box" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="font-weight: 800; margin-bottom: 0.5rem;">No Cargo Found</p>
            <p style="font-size: 0.9rem;">You don't have any cargo entries in the warehouse yet.</p>
        </div>
    @endif
</div>

<!-- Pagination -->
@if($cargos->hasPages())
<div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
    @if($cargos->onFirstPage())
        <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">« Previous</span>
    @else
        <a href="{{ $cargos->previousPageUrl() }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">« Previous</a>
    @endif

    @foreach($cargos->getUrlRange(max(1, $cargos->currentPage() - 2), min($cargos->lastPage(), $cargos->currentPage() + 2)) as $page => $url)
        @if($page === $cargos->currentPage())
            <span style="padding: 0.5rem 0.75rem; border-radius: 6px; background: var(--primary-green); color: white; font-size: 0.8rem; font-weight: 700;">{{ $page }}</span>
        @else
            <a href="{{ $url }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">{{ $page }}</a>
        @endif
    @endforeach

    @if($cargos->hasMorePages())
        <a href="{{ $cargos->nextPageUrl() }}" style="padding: 0.5rem 0.75rem; border-radius: 6px; background: #f8fafc; color: #475569; text-decoration: none; font-size: 0.8rem; font-weight: 700;">Next »</a>
    @else
        <span style="padding: 0.5rem 0.75rem; border-radius: 6px; color: #94a3b8; font-size: 0.8rem;">Next »</span>
    @endif
</div>
@endif

@endsection
