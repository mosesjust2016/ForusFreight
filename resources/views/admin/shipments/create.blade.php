@extends('layouts.dashboard')

@section('title', 'Create New Shipment - Admin')

@section('styles')
<style>
    .create-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        max-width: 720px;
        margin: 0 auto;
    }

    .create-card h2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
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
        box-sizing: border-box;
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

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* ── Image upload zone ── */
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .upload-zone.drag-over { border-color: var(--primary-green); background: #f0fdf4; }
    .upload-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .upload-zone-icon { font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem; }
    .upload-zone p { font-size: 0.82rem; color: #64748b; margin: 0; }
    .upload-zone span { font-size: 0.72rem; color: #94a3b8; }
    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .preview-thumb {
        position: relative; border-radius: 10px; overflow: hidden;
        aspect-ratio: 1; background: #f1f5f9;
    }
    .preview-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .preview-thumb .remove-preview {
        position: absolute; top: 4px; right: 4px;
        background: rgba(0,0,0,0.55); color: white; border: none;
        border-radius: 50%; width: 22px; height: 22px; font-size: 0.65rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }
    .preview-thumb .remove-preview:hover { background: #dc2626; }
    .preview-filename {
        font-size: 0.65rem; color: #64748b; text-align: center;
        padding: 0.2rem 0.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">
                Create New Shipment
            </h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Assign a freight shipment to an existing client account.</p>
        </div>
        <a href="{{ route('admin.shipments') }}" style="background: white; border: 1.5px solid #e2e8f0; color: var(--text-dark); padding: 0.6rem 1.25rem; border-radius: 12px; font-size: 0.85rem; font-weight: 700; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Shipments
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert-error" style="max-width: 720px; margin: 0 auto 1.5rem;">
        <i class="fas fa-triangle-exclamation"></i>
        <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="create-card">
    <h2><i class="fas fa-truck-ramp-box" style="color: var(--primary-green);"></i> Shipment Details</h2>

    <form method="POST" action="{{ route('admin.shipments.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Assign to Client</label>
            <select name="user_id" required>
                <option value="">— Select a client —</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }} ({{ $client->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Origin</label>
                <input type="text" name="origin" value="{{ old('origin') }}" placeholder="e.g. Durban, South Africa" required>
                @error('origin')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="destination" value="{{ old('destination') }}" placeholder="e.g. Lusaka, Zambia" required>
                @error('destination')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Cargo / Service Type</label>
                <input type="text" name="service" value="{{ old('service') }}" placeholder="e.g. General Cargo, Refrigerated" required>
                @error('service')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Initial Status</label>
                <select name="status" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', 'Order Placed') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @error('status')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Cost (ZMW)</label>
                <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', 0) }}" required>
                @error('cost')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Estimated Delivery</label>
                <input type="date" name="estimated_delivery" value="{{ old('estimated_delivery') }}">
                @error('estimated_delivery')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Cargo Images --}}
        <div class="form-group" style="margin-top:0.5rem;">
            <label>Cargo Images <span style="font-weight:500;text-transform:none;color:#94a3b8;">(optional — JPEG, PNG, WebP · max 5 MB each)</span></label>
            <div class="upload-zone" id="uploadZone" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <input type="file" name="images[]" id="imageInput" multiple accept="image/jpeg,image/png,image/jpg,image/webp" onchange="handleFiles(this.files)">
                <div class="upload-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                <p>Drag & drop images here, or <strong>click to browse</strong></p>
                <span>Supports: JPG, PNG, WebP</span>
            </div>
            <div class="preview-grid" id="previewGrid"></div>
            @error('images.*')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-save">
            <i class="fas fa-truck"></i> Create Shipment & Add Tracking Events
        </button>
    </form>
</div>

<script>
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
                <div class="preview-thumb">
                    <img src="${url}" alt="${file.name}">
                    <button type="button" class="remove-preview" onclick="removeStaged(${i})" title="Remove">
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
