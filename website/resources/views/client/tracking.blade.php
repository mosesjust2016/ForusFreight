@extends('layouts.dashboard')

@section('title', 'Shipment Tracking - Client Portal')

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Shipment Live-Tracking</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Real-time telemetry and milestone audit for <strong>#{{ $shipment->tracking_number }}</strong></p>
        </div>
        <div style="text-align: right;">
            <span style="background: var(--primary-green-light); color: var(--primary-green); padding: 0.5rem 1rem; border-radius: 10px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase;">
                {{ $shipment->status }}
            </span>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Left Column: Operations -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Interactive Milestone Tracker -->
        @php
            $progress = match(strtolower($shipment->status)) {
                'order placed' => 5,
                'processing' => 30,
                'at border' => 45,
                'customs clearance' => 60,
                'in transit' => 80,
                'out for delivery' => 95,
                'delivered' => 100,
                default => 0
            };
        @endphp
        <div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <h4 style="font-weight: 800; color: var(--text-dark); font-size: 1.1rem;"><i class="fas fa-route" style="color: var(--primary-green); margin-right: 0.75rem;"></i> Logistics Pipeline</h4>
                <div style="text-align: right;">
                    <div style="font-size: 1.25rem; font-weight: 900; color: var(--primary-green);">{{ $progress }}%</div>
                    <div style="font-size: 0.65rem; color: var(--text-gray); font-weight: 700; text-transform: uppercase;">Completion</div>
                </div>
            </div>
            
            <div style="position: relative; height: 10px; background: #f1f5f9; border-radius: 20px; margin-bottom: 3rem;">
                <div style="position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #007f7f, #00bfa5); border-radius: 20px; width: {{ $progress }}%; transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                <div style="position: absolute; top: -18px; left: {{ $progress }}%; transform: translateX(-50%); background: white; border: 4px solid var(--primary-green); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-green); box-shadow: 0 8px 20px rgba(0,127,127,0.3); transition: left 1.5s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 10;">
                    <i class="fas fa-truck" style="font-size: 1.2rem;"></i>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); text-align: center; font-size: 0.75rem; font-weight: 800; color: #94a3b8;">
                <div style="{{ $progress >= 5 ? 'color: var(--primary-green);' : '' }}">
                    <div style="width: 12px; height: 12px; background: {{ $progress >= 5 ? 'var(--primary-green)' : '#e2e8f0' }}; border-radius: 50%; margin: 0 auto 1rem;"></div>
                    ORDER PLACED
                </div>
                <div style="{{ $progress >= 45 ? 'color: var(--primary-green);' : '' }}">
                    <div style="width: 12px; height: 12px; background: {{ $progress >= 45 ? 'var(--primary-green)' : '#e2e8f0' }}; border-radius: 50%; margin: 0 auto 1rem;"></div>
                    BORDER HUB
                </div>
                <div style="{{ $progress >= 80 ? 'color: var(--primary-green);' : '' }}">
                    <div style="width: 12px; height: 12px; background: {{ $progress >= 80 ? 'var(--primary-green)' : '#e2e8f0' }}; border-radius: 50%; margin: 0 auto 1rem;"></div>
                    IN TRANSIT
                </div>
                <div style="{{ $progress >= 100 ? 'color: var(--primary-green);' : '' }}">
                    <div style="width: 12px; height: 12px; background: {{ $progress >= 100 ? 'var(--primary-green)' : '#e2e8f0' }}; border-radius: 50%; margin: 0 auto 1rem;"></div>
                    DELIVERED
                </div>
            </div>
        </div>

        <!-- Live Map -->
        <div style="background: white; border-radius: 24px; overflow: hidden; box-shadow: var(--shadow); height: 500px; position: relative; border: 1px solid #f1f5f9;">
            <div id="trackingMap" style="width: 100%; height: 100%; z-index: 1;"></div>
            
            @php
                $latestEvent = $shipment->trackingEvents->sortByDesc('event_time')->first();
            @endphp
            <div style="position: absolute; bottom: 1.5rem; left: 1.5rem; z-index: 100; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 280px; border: 1px solid white;">
                <h4 style="font-size: 0.9rem; font-weight: 800; margin-bottom: 1rem; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-satellite" style="color: var(--primary-green);"></i> Last Update
                </h4>
                @if($latestEvent)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; gap: 0.5rem;">
                        <span style="color: var(--text-gray); font-weight: 600; white-space: nowrap;">Location</span>
                        <span style="font-weight: 800; color: var(--primary-green); text-align: right;">{{ $latestEvent->location }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; gap: 0.5rem;">
                        <span style="color: var(--text-gray); font-weight: 600; white-space: nowrap;">Update</span>
                        <span style="font-weight: 800; color: var(--text-dark); text-align: right;">{{ $latestEvent->description }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem;">
                        <span style="color: var(--text-gray); font-weight: 600;">Time</span>
                        <span style="font-weight: 800; color: var(--text-dark);">{{ $latestEvent->event_time->diffForHumans() }}</span>
                    </div>
                </div>
                @else
                <p style="font-size: 0.8rem; color: var(--text-gray);">No updates yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Details & Actions -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- ETA Highlight -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 2rem; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <p style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.1em;">Expected Arrival</p>
            <h3 style="font-size: 1.75rem; font-weight: 900; margin-bottom: 0.25rem; color: white;">{{ $shipment->estimated_delivery->format('M d, Y') }}</h3>
            <p style="font-size: 0.9rem; font-weight: 600; color: var(--primary-green);">{{ $shipment->estimated_delivery->diffForHumans() }}</p>
            
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #94a3b8;">Driver ID:</span>
                    <span style="font-size: 0.8rem; font-weight: 700;">#ZM-DR-882</span>
                </div>
            </div>
        </div>

        <!-- Real-time Activity Feed -->
        <div style="background: white; border-radius: 24px; padding: 1.5rem; flex-grow: 1; box-shadow: var(--shadow); border: 1px solid #f1f5f9;">
            <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--text-dark);">Live Audit Log</h4>
            <div style="display: flex; flex-direction: column; gap: 1.75rem; position: relative;">
                <div style="position: absolute; left: 5px; top: 10px; bottom: 10px; width: 2px; background: #f1f5f9; z-index: 1;"></div>
                
                @forelse($shipment->trackingEvents as $event)
                <div style="display: flex; gap: 1.25rem; position: relative; z-index: 2;">
                    <div style="width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; flex-shrink: 0; margin-top: 6px; border: 3px solid white; box-shadow: 0 0 0 1px var(--primary-green);"></div>
                    <div>
                        <p style="font-size: 0.85rem; font-weight: 800; margin-bottom: 0.15rem; color: var(--text-dark);">{{ $event->description }}</p>
                        <p style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $event->location }}</p>
                        <p style="font-size: 0.65rem; color: var(--text-gray); margin-top: 0.25rem;">{{ $event->event_time->format('h:i A, M d') }}</p>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 2rem 0;">
                    <i class="fas fa-satellite-dish" style="font-size: 2rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                    <p style="font-size: 0.8rem; color: var(--text-gray);">Awaiting initial beacon scan...</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Action Grid -->
        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
            <a href="#" style="background: white; border: 1.5px solid #e2e8f0; color: var(--text-dark); padding: 1rem; border-radius: 15px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.75rem; font-weight: 800; font-size: 0.85rem;">
                <i class="fas fa-file-invoice" style="color: #64748b;"></i> PROOF OF DELIVERY
            </a>
            <a href="https://wa.me/?text=Status%20Update%3A%20Shipment%20{{ $shipment->tracking_number }}%20is%20{{ $shipment->status }}" style="background: #25d366; color: white; padding: 1rem; border-radius: 15px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.75rem; font-weight: 800; font-size: 0.85rem;">
                <i class="fab fa-whatsapp"></i> NOTIFY TEAM
            </a>
        </div>
    </div>
</div>

<!-- Map Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Build waypoints from tracking events that have coordinates
        @php
            $mapEvents = $shipment->trackingEvents
                ->sortBy('event_time')
                ->filter(fn($e) => $e->latitude && $e->longitude)
                ->values()
                ->map(fn($e) => [
                    'lat'         => (float) $e->latitude,
                    'lng'         => (float) $e->longitude,
                    'location'    => $e->location,
                    'description' => $e->description,
                    'status'      => $e->status,
                    'time'        => $e->event_time->format('M d, Y · h:i A'),
                ])->values();
        @endphp
        var trackingEvents = @json($mapEvents);

        var defaultCenter = [-15.3875, 28.3228];
        var map = L.map('trackingMap', {
            center: defaultCenter,
            zoom: 5,
            zoomControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.control.zoom({ position: 'topright' }).addTo(map);

        var warehouseIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background:white;border:3px solid #007f7f;width:18px;height:18px;border-radius:50%;box-shadow:0 4px 10px rgba(0,0,0,0.12);'></div>",
            iconSize: [18, 18], iconAnchor: [9, 9]
        });

        var truckIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background:#ff6200;border:3px solid white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 8px 20px rgba(255,98,0,0.3);'><i class='fas fa-truck' style='font-size:16px;'></i></div>",
            iconSize: [36, 36], iconAnchor: [18, 18]
        });

        if (trackingEvents.length > 0) {
            // Draw route through all event coordinates
            var latlngs = trackingEvents.map(function(e) { return [e.lat, e.lng]; });
            var polyline = L.polyline(latlngs, {color: '#007f7f', weight: 4, dashArray: '10, 10'}).addTo(map);

            // Waypoint markers
            trackingEvents.forEach(function(e, idx) {
                var isLast = idx === trackingEvents.length - 1;
                var icon = isLast ? truckIcon : warehouseIcon;
                var popup = '<strong>' + e.location + '</strong><br>' + e.description
                          + (e.status ? '<br><em>' + e.status + '</em>' : '')
                          + '<br><small>' + e.time + '</small>';
                var marker = L.marker([e.lat, e.lng], {icon: icon}).addTo(map).bindPopup(popup);
                if (isLast) marker.openPopup();
            });

            map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
        } else {
            // No coordinates yet — show a placeholder centered on the region
            map.setView(defaultCenter, 5);
            L.divIcon({});
            var placeholder = L.divIcon({
                className: '',
                html: "<div style='background:white;padding:0.75rem 1.25rem;border-radius:12px;font-size:0.8rem;font-weight:700;color:#64748b;box-shadow:0 4px 15px rgba(0,0,0,0.1);white-space:nowrap;'><i class='fas fa-satellite-dish' style='color:#007f7f;margin-right:0.5rem;'></i>Awaiting GPS data from admin</div>",
                iconSize: [260, 40], iconAnchor: [130, 20]
            });
            L.marker(defaultCenter, {icon: placeholder}).addTo(map);
        }
    });
</script>
@endsection
