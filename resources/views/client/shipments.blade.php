@extends('layouts.dashboard')

@section('title', 'Shipments - Forus Freight')

@section('styles')
<style>
    .s-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .s-head h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        letter-spacing: -0.02em;
    }

    .s-tabs {
        display: inline-flex;
        gap: 0.25rem;
        padding: 0.25rem;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
    }

    .s-tabs a {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 38px;
        padding: 0 0.9rem;
        border-radius: 9px;
        color: var(--ink-2);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
    }

    .s-tabs a:hover { color: var(--ink); }

    .s-tabs a.is-active {
        background: var(--ink);
        color: #fff;
    }

    .s-tabs a small {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .s-list {
        list-style: none;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        overflow: hidden;
    }

    .s-row {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) auto;
        gap: 0.5rem 1.5rem;
        align-items: center;
        padding: 1.1rem 1.4rem;
        color: inherit;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }

    .s-list li + li .s-row { border-top: 1px solid var(--line); }

    .s-row:hover { background: #fafaf7; }

    .s-ref {
        font-family: var(--mono);
        font-weight: 500;
    }

    .s-desc,
    .s-meta {
        display: block;
        font-size: 0.85rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .s-progress {
        display: grid;
        gap: 0.4rem;
    }

    .s-status {
        font-size: 0.82rem;
        font-weight: 600;
        color: #005e5e;
    }

    .s-status.is-done { color: #166534; }
    .s-status.is-alert { color: #92400e; }

    .s-bar {
        height: 5px;
        border-radius: 5px;
        background: var(--paper-2);
        overflow: hidden;
    }

    .s-bar span {
        display: block;
        height: 100%;
        border-radius: 5px;
        background: linear-gradient(90deg, var(--teal), var(--teal-bright));
    }

    .s-end {
        text-align: right;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .s-end-text { font-size: 0.85rem; color: var(--muted); }
    .s-end-text strong { display: block; color: var(--ink); font-size: 0.92rem; }

    .s-chevron { color: #aab4b1; }

    .s-empty {
        padding: 2.5rem 1.5rem;
        text-align: center;
        background: #fff;
        border: 1px dashed var(--line-strong);
        border-radius: 18px;
        color: var(--ink-2);
    }

    .s-empty a { color: var(--teal-deep); font-weight: 700; }

    .s-pages {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .s-pages a,
    .s-pages span {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 1rem;
        border-radius: 10px;
        border: 1px solid var(--line-strong);
        background: #fff;
        color: var(--ink);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .s-pages span { color: var(--muted); border-color: transparent; background: none; }

    @media (max-width: 720px) {
        .s-row {
            grid-template-columns: minmax(0, 1fr) auto;
            padding: 1rem 1.1rem;
        }

        .s-progress { grid-column: 1 / -1; grid-row: 2; }
        .s-end-text { display: none; }
    }
</style>
@endsection

@section('content')
    <div class="s-head">
        <h1>Shipments</h1>
        <nav class="s-tabs" aria-label="Filter shipments">
            <a href="{{ route('client.shipments') }}" class="{{ $show === 'active' ? 'is-active' : '' }}" @if($show === 'active') aria-current="page" @endif>On the way <small>{{ $counts['active'] }}</small></a>
            <a href="{{ route('client.shipments', ['show' => 'delivered']) }}" class="{{ $show === 'delivered' ? 'is-active' : '' }}" @if($show === 'delivered') aria-current="page" @endif>Delivered <small>{{ $counts['delivered'] }}</small></a>
        </nav>
    </div>

    @if($shipments->isEmpty())
        <div class="s-empty">
            @if($show === 'active')
                <p>Nothing on the way right now.</p>
                <p style="margin-top: 0.5rem;">
                    @if($counts['delivered'] > 0)
                        See your <a href="{{ route('client.shipments', ['show' => 'delivered']) }}">delivered shipments</a>, or <a href="{{ route('client.shipments.create') }}">tell us what’s coming</a>.
                    @else
                        When your parcels reach our China warehouse they’ll appear here — or <a href="{{ route('client.shipments.create') }}">tell us what’s coming</a>.
                    @endif
                </p>
            @else
                <p>No delivered shipments yet.</p>
            @endif
        </div>
    @else
        <ul class="s-list">
            @foreach($shipments as $shipment)
                @php
                    $canon = \App\Models\Shipment::canonicalStatus($shipment->status);
                    $tone = $canon === 'DELIVERED' ? 'is-done' : (in_array($canon, ['ON_HOLD', 'EXCEPTION'], true) ? 'is-alert' : '');
                    $pct = max(0, min(100, (int) $shipment->delivery_progress_percent));
                @endphp
                <li>
                    <a class="s-row" href="{{ route('tracking.show', $shipment->serial_no) }}">
                        <span style="min-width: 0;">
                            <span class="s-ref">{{ $shipment->serial_no }}</span>
                            <span class="s-desc">{{ $shipment->description ?: 'Shipment' }}</span>
                        </span>
                        <span class="s-progress">
                            <span class="s-status {{ $tone }}">{{ $shipment->status_label }}</span>
                            <span class="s-bar" aria-hidden="true"><span style="width: {{ $pct }}%;"></span></span>
                        </span>
                        <span class="s-end">
                            <span class="s-end-text">
                                @if($canon === 'DELIVERED')
                                    Delivered
                                @elseif($shipment->estimated_delivery)
                                    Arrives <strong>{{ $shipment->estimated_delivery->format('j M') }}</strong>
                                @else
                                    {{ $shipment->origin }} → {{ $shipment->destination }}
                                @endif
                                @if($shipment->cost > 0)
                                    <span style="display: block;">{{ usd($shipment->cost) }}</span>
                                @endif
                            </span>
                            <i class="fas fa-chevron-right s-chevron" aria-hidden="true"></i>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        @if($shipments->hasPages())
            <nav class="s-pages" aria-label="Pages">
                @if($shipments->previousPageUrl())<a href="{{ $shipments->previousPageUrl() }}">← Newer</a>@endif
                <span>Page {{ $shipments->currentPage() }} of {{ $shipments->lastPage() }}</span>
                @if($shipments->nextPageUrl())<a href="{{ $shipments->nextPageUrl() }}">Older →</a>@endif
            </nav>
        @endif
    @endif
@endsection
