@extends('layouts.dashboard')

@section('title', 'Deal Stages - CRM - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .stage-row { transition: all 0.2s; }
    .stage-row:hover { background: #fcfdfe; }
    .stage-row td { padding: 1.25rem 1rem; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .stage-row td:first-child { border-left: 1px solid #f8fafc; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .stage-row td:last-child { border-right: 1px solid #f8fafc; border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    .color-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-block; margin-right: 0.5rem; vertical-align: middle; }
    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .form-inline { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .form-inline input, .form-inline select { padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Deal Stages</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Customize your sales pipeline stages, probabilities, and closure rules.</p>
        </div>
    </div>
</div>

<!-- Add Stage Form -->
<div class="crm-grid" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;">Add New Stage</h2>
    <form action="{{ route('admin.crm.stages.store') }}" method="POST" class="form-inline">
        @csrf
        <input type="text" name="name" placeholder="Stage name" required>
        <input type="color" name="color" value="#4caf50" title="Stage color">
        <input type="number" name="position" placeholder="Position" min="0">
        <input type="number" name="win_probability" placeholder="Win %" min="0" max="100" step="0.01">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #475569;">
            <input type="checkbox" name="is_closed" value="1"> Closed
        </label>
        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #475569;">
            <input type="checkbox" name="is_won" value="1"> Won
        </label>
        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Add Stage</button>
    </form>
</div>

<!-- Stages List -->
<div class="crm-grid">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
        <thead>
            <tr style="text-align: left;">
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Stage</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Color</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Position</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Win Probability</th>
                <th style="padding: 0 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stages as $stage)
            <tr class="stage-row">
                <td style="padding: 1.25rem 1rem; font-weight: 800; color: var(--text-dark);">{{ $stage->name }}</td>
                <td style="padding: 1.25rem 1rem;"><span class="color-dot" style="background: {{ $stage->color }};"></span> {{ $stage->color }}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ $stage->position }}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 700; color: #475569;">{{ $stage->win_probability }}%</td>
                <td style="padding: 1.25rem 1rem;">
                    @if($stage->is_closed)
                        <span class="status-badge" style="background: #fef2f2; color: #ef4444;">Closed {{ $stage->is_won ? '(Won)' : '(Lost)' }}</span>
                    @else
                        <span class="status-badge" style="background: #f0fdf4; color: #16a34a;">Open</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 4rem 0; color: var(--text-gray);">
                    <i class="fas fa-layer-group" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 800;">No Stages Defined</p>
                    <p style="font-size: 0.85rem;">Add your first pipeline stage above.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
