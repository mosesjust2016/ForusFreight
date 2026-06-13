@extends('layouts.dashboard')

@section('title', 'Edit Shipment #' . $shipment->tracking_number . ' - Admin')

@section('styles')
<style>
    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .edit-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: var(--shadow);
    }

    .edit-card h2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-gray);
        margin-bottom: 0.5rem;
        letter-spacing: 0.04em;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--primary-green);
    }

    .btn-save {
        width: 100%;
        padding: 0.9rem;
        background: var(--primary-green);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 800;
        cursor: pointer;
        transition: opacity 0.2s;
        margin-top: 0.5rem;
    }

    .btn-save:hover { opacity: 0.9; }

    .event-timeline {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-top: 1.5rem;
        position: relative;
    }

    .event-timeline::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #f1f5f9;
    }

    .event-item {
        display: flex;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .event-dot {
        width: 12px;
        height: 12px;
        background: var(--primary-green);
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 5px;
        border: 3px solid white;
        box-shadow: 0 0 0 1px var(--primary-green);
    }

    .event-content p {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.15rem;
    }

    .event-content span {
        font-size: 0.75rem;
        color: var(--text-gray);
        font-weight: 600;
        display: block;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #166534;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* ── Image upload zone ── */
    .upload-zone {
        border: 2px dashed #cbd5e1; border-radius: 14px; padding: 1.5rem 1rem;
        text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .upload-zone.drag-over { border-color: var(--primary-green); background: #f0fdf4; }
    .upload-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .upload-zone-icon { font-size: 1.6rem; color: #94a3b8; margin-bottom: 0.4rem; }
    .upload-zone p { font-size: 0.8rem; color: #64748b; margin: 0; }
    .upload-zone span { font-size: 0.7rem; color: #94a3b8; }
    .img-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 0.65rem; margin-top: 0.75rem;
    }
    .img-thumb {
        position: relative; border-radius: 10px; overflow: hidden;
        aspect-ratio: 1; background: #f1f5f9; border: 1.5px solid #e2e8f0;
    }
    .img-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; cursor: pointer; }
    .img-thumb .btn-rm {
        position: absolute; top: 3px; right: 3px;
        background: rgba(0,0,0,0.55); color: white; border: none;
        border-radius: 50%; width: 20px; height: 20px; font-size: 0.6rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .img-thumb .btn-rm:hover { background: #dc2626; }
    .preview-filename { font-size: 0.62rem; color: #64748b; text-align: center; padding: 0.15rem 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* lightbox */
    #lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; }
    #lightbox.active { display:flex; }
    #lightbox img { max-width:90vw; max-height:90vh; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,0.5); }
    #lightbox-close { position:absolute; top:1.25rem; right:1.5rem; color:white; font-size:1.75rem; cursor:pointer; background:none; border:none; }

    @media (max-width: 900px) {
        .edit-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">
                Edit Shipment
            </h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">
                Tracking: <strong>#{{ $shipment->tracking_number }}</strong>
                &nbsp;&middot;&nbsp; Client: <strong>{{ $shipment->user?->name ?? 'N/A' }}</strong>
            </p>
        </div>
        <a href="{{ route('admin.shipments') }}" style="background: white; border: 1.5px solid #e2e8f0; color: var(--text-dark); padding: 0.6rem 1.25rem; border-radius: 12px; font-size: 0.85rem; font-weight: 700; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Shipments
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="edit-grid">
    <!-- Left: Shipment Details Form -->
    <div class="edit-card">
        <h2><i class="fas fa-pen-to-square" style="color: var(--primary-green);"></i> Shipment Details</h2>

        <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $shipment->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @error('status')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Origin</label>
                <input type="text" name="origin" value="{{ old('origin', $shipment->origin) }}" placeholder="e.g. Durban, South Africa">
                @error('origin')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="destination" value="{{ old('destination', $shipment->destination) }}" placeholder="e.g. Lusaka, Zambia">
                @error('destination')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Current Border / Hub</label>
                <input type="text" name="current_border" value="{{ old('current_border', $shipment->current_border) }}" placeholder="e.g. Beitbridge">
                @error('current_border')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Cost (ZMW)</label>
                <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $shipment->cost) }}">
                @error('cost')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Estimated Delivery</label>
                <input type="date" name="estimated_delivery" value="{{ old('estimated_delivery', $shipment->estimated_delivery?->format('Y-m-d')) }}">
                @error('estimated_delivery')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            {{-- Existing images --}}
            @if(!empty($shipment->images))
            <div class="form-group">
                <label>Current Images ({{ count($shipment->images) }})</label>
                <div class="img-grid">
                    @foreach($shipment->images as $i => $path)
                    <div class="img-thumb">
                        <img src="{{ Storage::url($path) }}" alt="Cargo image {{ $i + 1 }}"
                             onclick="openLightbox('{{ Storage::url($path) }}')">
                        <form method="POST" action="{{ route('admin.shipments.images.remove', $shipment) }}" style="display:contents;">
                            @csrf
                            <input type="hidden" name="index" value="{{ $i }}">
                            <button type="submit" class="btn-rm" title="Remove image"
                                    onclick="return confirm('Remove this image?')">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upload new images --}}
            <div class="form-group">
                <label>Add More Images <span style="font-weight:500;text-transform:none;color:#94a3b8;">(optional)</span></label>
                <div class="upload-zone" id="uploadZone" ondragover="handleDragOver(event)" ondragleave="handleDragLeave()" ondrop="handleDrop(event)">
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/jpeg,image/png,image/jpg,image/webp" onchange="handleFiles(this.files)">
                    <div class="upload-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                    <p>Drag & drop or <strong>click to browse</strong></p>
                    <span>JPG, PNG, WebP · max 5 MB each</span>
                </div>
                <div class="img-grid" id="previewGrid"></div>
                @error('images.*')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Right: Add Tracking Event + Timeline -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="edit-card" id="add-event">
            <h2><i class="fas fa-location-dot" style="color: var(--primary-green);"></i> Add Tracking Event</h2>

            <form method="POST" action="{{ route('admin.shipments.events.store', $shipment) }}">
                @csrf

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. Beitbridge Border Post" value="{{ old('location') }}">
                    @error('location')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Event Description</label>
                    <input type="text" name="description" placeholder="e.g. Shipment cleared customs" value="{{ old('description') }}">
                    @error('description')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Update Shipment Status <span style="font-weight:500;text-transform:none;color:#94a3b8;">(optional — updates the shipment's status)</span></label>
                    <select name="status">
                        <option value="">— Keep current status —</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ old('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('status')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Latitude <span style="font-weight:500;text-transform:none;color:#94a3b8;">(optional)</span></label>
                        <input type="number" step="0.0000001" name="latitude" placeholder="-15.3875" value="{{ old('latitude') }}">
                        @error('latitude')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Longitude <span style="font-weight:500;text-transform:none;color:#94a3b8;">(optional)</span></label>
                        <input type="number" step="0.0000001" name="longitude" placeholder="28.3228" value="{{ old('longitude') }}">
                        @error('longitude')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <p style="font-size:0.7rem;color:#94a3b8;margin:0.4rem 0 1rem;">Coordinates place the location on the client's live map.</p>

                <div class="form-group">
                    <label>Event Date & Time</label>
                    <input type="datetime-local" name="event_time" value="{{ old('event_time', now()->format('Y-m-d\TH:i')) }}">
                    @error('event_time')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-plus"></i> Add Event
                </button>
            </form>
        </div>

        <div class="edit-card">
            <h2><i class="fas fa-route" style="color: var(--primary-green);"></i> Tracking Timeline</h2>

            @if($shipment->trackingEvents->isEmpty())
                <div style="text-align: center; padding: 2rem 0; color: var(--text-gray);">
                    <i class="fas fa-satellite-dish" style="font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.3; display: block;"></i>
                    <p style="font-size: 0.85rem;">No tracking events yet. Add one above.</p>
                </div>
            @else
                <div class="event-timeline">
                    @foreach($shipment->trackingEvents as $event)
                        <div class="event-item" style="justify-content: space-between; align-items: flex-start;">
                            <div style="display: flex; gap: 1rem;">
                                <div class="event-dot"></div>
                                <div class="event-content">
                                    <p>{{ $event->description }}</p>
                                    <span><i class="fas fa-location-dot" style="font-size:0.65rem;"></i> {{ $event->location }}</span>
                                    @if($event->status)
                                        <span style="color: var(--primary-green); font-weight: 800;">
                                            <i class="fas fa-circle-dot" style="font-size:0.65rem;"></i> {{ $event->status }}
                                        </span>
                                    @endif
                                    @if($event->latitude && $event->longitude)
                                        <span style="font-size:0.65rem; color:#94a3b8;">
                                            <i class="fas fa-map-pin"></i> {{ $event->latitude }}, {{ $event->longitude }}
                                        </span>
                                    @endif
                                    <span>{{ $event->event_time->format('M d, Y · h:i A') }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.shipments.events.destroy', [$shipment, $event]) }}"
                                  onsubmit="return confirm('Remove this tracking event?');" style="flex-shrink:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;" title="Delete event">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" onclick="closeLightbox()">
    <button id="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
    <img id="lightbox-img" src="" alt="Preview">
</div>

<script>
    /* ── Lightbox ── */
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('active');
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

    /* ── New image staging ── */
    let stagedFiles = new DataTransfer();

    function syncInput() {
        document.getElementById('imageInput').files = stagedFiles.files;
    }

    function renderPreviews() {
        const grid = document.getElementById('previewGrid');
        grid.innerHTML = '';
        Array.from(stagedFiles.files).forEach((file, i) => {
            const url = URL.createObjectURL(file);
            grid.insertAdjacentHTML('beforeend', `
                <div class="img-thumb">
                    <img src="${url}" alt="${file.name}" onclick="openLightbox('${url}')">
                    <button type="button" class="btn-rm" onclick="removeStaged(${i})" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="preview-filename">${file.name}</div>
                </div>`);
        });
    }

    function handleFiles(files) {
        Array.from(files).forEach(f => stagedFiles.items.add(f));
        syncInput();
        renderPreviews();
    }

    function removeStaged(index) {
        const newDt = new DataTransfer();
        Array.from(stagedFiles.files).forEach((f, i) => { if (i !== index) newDt.items.add(f); });
        stagedFiles = newDt;
        syncInput();
        renderPreviews();
    }

    function handleDragOver(e) {
        e.preventDefault();
        document.getElementById('uploadZone').classList.add('drag-over');
    }
    function handleDragLeave() {
        document.getElementById('uploadZone').classList.remove('drag-over');
    }
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('uploadZone').classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    }
</script>
@endsection
