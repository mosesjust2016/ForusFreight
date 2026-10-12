{{-- Shipment status card: status, route + progress, key facts, timeline
     (newest first) and photos. Used by the public tracking page and the
     client portal's shipment page. Expects $shipment with trackingEvents; optional $ref. --}}
@php
    // $ref lets a page choose which identifier to headline (the portal uses serial numbers)
    $trackingRef = $ref ?? ($shipment->tracking_number ?: $shipment->serial_no);
    $canonical = \App\Models\Shipment::canonicalStatus($shipment->status);
    $tone = match (true) {
        $canonical === 'DELIVERED' => 'done',
        in_array($canonical, ['ON_HOLD', 'EXCEPTION'], true) => 'alert',
        default => 'active',
    };
    // Newest first: the top entry is where the shipment is now
    $events = $shipment->trackingEvents->reverse()->values();
    $images = is_string($shipment->images) ? json_decode($shipment->images, true) : ($shipment->images ?? []);
    $progress = max(0, min(100, (int) $shipment->delivery_progress_percent));
@endphp
@once
<style>
    .t-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 24px;
        overflow: hidden;
    }

    .t-card-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem 2rem;
        padding: 2rem 2.25rem 1.75rem;
        border-bottom: 1px solid var(--line);
    }

    .t-ref-label {
        font-family: var(--mono);
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .t-ref {
        font-family: var(--mono);
        font-weight: 500;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        letter-spacing: 0.02em;
        margin-top: 0.25rem;
        word-break: break-all;
    }

    .t-status {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 1rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .t-status::before {
        content: '';
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: currentColor;
    }

    .t-status--active { background: #e6f6f4; color: #005e5e; }
    .t-status--done { background: #dcfce7; color: #166534; }
    .t-status--alert { background: #fef3c7; color: #92400e; }

    .t-updated {
        margin-top: 0.4rem;
        font-size: 0.85rem;
        color: var(--muted);
        text-align: right;
    }

    /* Route with progress */
    .t-route {
        padding: 1.75rem 2.25rem;
        border-bottom: 1px solid var(--line);
    }

    .t-route-ends {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.9rem;
    }

    .t-route-ends small {
        display: block;
        font-family: var(--mono);
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .t-route-ends strong {
        font-family: var(--display);
        font-stretch: 105%;
        font-size: 1.2rem;
    }

    .t-route-ends > div:last-child {
        text-align: right;
    }

    .t-bar {
        position: relative;
        height: 10px;
        border-radius: 10px;
        background: var(--paper-2);
    }

    .t-bar span {
        position: absolute;
        inset: 0 auto 0 0;
        border-radius: 10px;
        background: linear-gradient(90deg, var(--teal), var(--orange));
    }

    .t-bar span::after {
        content: '';
        position: absolute;
        right: -7px;
        top: 50%;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        border-radius: 50%;
        background: #fff;
        border: 4px solid var(--orange);
    }

    .t-progress-label {
        margin-top: 0.85rem;
        font-size: 0.9rem;
        color: var(--ink-2);
    }

    .t-progress-label strong {
        font-family: var(--mono);
        color: var(--ink);
    }

    /* Facts + timeline */
    .t-body {
        display: grid;
        grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);
    }

    .t-facts {
        padding: 1.75rem 2.25rem;
        border-right: 1px solid var(--line);
    }

    .t-facts dl div {
        padding: 0.9rem 0;
        border-bottom: 1px dashed var(--line-strong);
    }

    .t-facts dl div:last-child {
        border-bottom: none;
    }

    .t-facts dt {
        font-family: var(--mono);
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 0.2rem;
    }

    .t-facts dd {
        font-weight: 600;
    }

    .t-eta {
        margin-top: 1rem;
        padding: 1.1rem 1.2rem;
        border-radius: 14px;
        background: var(--ink);
        color: #fff;
        display: flex;
        gap: 0.9rem;
        align-items: center;
    }

    .t-eta i {
        font-size: 1.2rem;
        color: var(--orange);
    }

    .t-eta small {
        display: block;
        font-family: var(--mono);
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #8fa6ad;
    }

    .t-eta strong {
        font-size: 1.02rem;
    }

    .t-timeline {
        padding: 1.75rem 2.25rem;
    }

    .t-section-label {
        font-family: var(--mono);
        font-size: 0.72rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 1.25rem;
    }

    .t-events {
        list-style: none;
        position: relative;
    }

    .t-events li {
        position: relative;
        padding: 0 0 1.6rem 2rem;
    }

    .t-events li::before {
        /* the line joining one event to the next */
        content: '';
        position: absolute;
        left: 6px;
        top: 14px;
        bottom: -2px;
        width: 2px;
        background: var(--line-strong);
    }

    .t-events li:last-child {
        padding-bottom: 0;
    }

    .t-events li:last-child::before {
        display: none;
    }

    .t-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--teal);
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px var(--teal);
    }

    .t-events li:first-child .t-dot {
        background: var(--orange);
        box-shadow: 0 0 0 4px rgba(255, 98, 0, 0.2);
    }

    .t-event-title {
        font-weight: 700;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .t-latest {
        font-family: var(--mono);
        font-size: 0.66rem;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: #fff1e6;
        color: #b54400;
        padding: 0.15rem 0.45rem;
        border-radius: 5px;
    }

    .t-event-meta {
        font-size: 0.9rem;
        color: var(--muted);
        margin-top: 0.15rem;
    }

    .t-empty {
        color: var(--muted);
    }

    .t-photos {
        padding: 1.75rem 2.25rem;
        border-top: 1px solid var(--line);
    }

    .t-photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }

    .t-photo-grid a {
        display: block;
        aspect-ratio: 4 / 3;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--line);
    }

    .t-photo-grid img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .t-photo-grid a:hover img {
        transform: scale(1.04);
    }

    @media (max-width: 860px) {
        .t-body {
            grid-template-columns: 1fr;
        }

        .t-facts {
            border-right: none;
            border-bottom: 1px solid var(--line);
        }
    }

    @media (max-width: 600px) {
        .t-card {
            border-radius: 18px;
        }

        .t-card-head,
        .t-route,
        .t-facts,
        .t-timeline,
        .t-photos {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .t-card-head {
            align-items: flex-start;
        }

        .t-updated {
            text-align: left;
        }
    }
</style>
@endonce
<article class="t-card">
    <header class="t-card-head">
        <div>
            <h2 class="t-ref-label" id="resultTitle">Shipment</h2>
            <p class="t-ref">{{ $trackingRef }}</p>
        </div>
        <div>
            <span class="t-status t-status--{{ $tone }}">{{ $shipment->status_label }}</span>
            @if($shipment->last_update_at)
                <p class="t-updated">Updated {{ $shipment->last_update_at->format('M j, Y · H:i') }}</p>
            @endif
        </div>
    </header>

    <div class="t-route">
        <div class="t-route-ends">
            <div><small>From</small><strong>{{ $shipment->origin ?: '—' }}</strong></div>
            <div><small>To</small><strong>{{ $shipment->destination ?: '—' }}</strong></div>
        </div>
        <div class="t-bar" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Delivery progress">
            <span style="width: {{ $progress }}%;"></span>
        </div>
        <p class="t-progress-label"><strong>{{ $progress }}%</strong> of the way there</p>
    </div>

    <div class="t-body">
        <div class="t-facts">
            <dl>
                <div><dt>Cargo</dt><dd>{{ $shipment->description ?: 'Not specified' }}</dd></div>
                <div><dt>Current location</dt><dd>{{ $shipment->current_location_display ?: 'Not available yet' }}</dd></div>
                <div><dt>Next action</dt><dd>{{ $shipment->next_action ?: 'No next action recorded' }}</dd></div>
                <div><dt>Days in transit</dt><dd>{{ $shipment->days_in_transit !== null ? $shipment->days_in_transit . ' day(s)' : 'Not recorded' }}</dd></div>
            </dl>
            <div class="t-eta">
                <i class="fas fa-truck-fast" aria-hidden="true"></i>
                <div>
                    <small>Estimated delivery</small>
                    <strong>
                        @if($shipment->estimated_delivery)
                            {{ $shipment->estimated_delivery->format('l, j F Y') }}
                        @else
                            To be confirmed
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        <div class="t-timeline">
            <h3 class="t-section-label">Shipment timeline</h3>
            @if($events->isEmpty())
                <p class="t-empty">No tracking updates yet. You'll see each stage here as soon as it's recorded.</p>
            @else
                <ol class="t-events">
                    @foreach($events as $i => $event)
                        <li>
                            <span class="t-dot" aria-hidden="true"></span>
                            <p class="t-event-title">
                                {{ $event->description }}
                                @if($i === 0)<span class="t-latest">Latest</span>@endif
                            </p>
                            <p class="t-event-meta">
                                @if($event->status){{ \App\Models\Shipment::statusLabelFor($event->status) }} &middot; @endif
                                {{ $event->location }}@if($event->location && $event->event_time) &middot; @endif{{ $event->event_time ? $event->event_time->format('M j, Y · H:i') : '' }}
                            </p>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    @if(!empty($images))
        <div class="t-photos">
            <h3 class="t-section-label">Shipment photos</h3>
            <div class="t-photo-grid">
                @foreach($images as $image)
                    <a href="{{ asset('storage/' . $image) }}" target="_blank" rel="noopener" aria-label="Open shipment photo {{ $loop->iteration }} in a new tab">
                        <img src="{{ asset('storage/' . $image) }}" alt="Shipment photo {{ $loop->iteration }}" loading="lazy">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</article>
