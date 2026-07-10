@extends('layouts.dashboard')

@section('title', 'Bulk Shipment Import - Forus Freight')

@section('styles')
<style>
    .import-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
    .upload-area { border: 2px dashed #e2e8f0; border-radius: 16px; padding: 3rem; text-align: center; cursor: pointer; transition: all 0.2s; background: #f8fafc; }
    .upload-area:hover { border-color: var(--primary-green); background: #f0fdf4; }
    .upload-area.dragover { border-color: var(--primary-green); background: #f0fdf4; }
    .file-input { display: none; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3); }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-secondary:hover { background: #e2e8f0; }
    .results-section { margin-top: 2rem; }
    .result-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; }
    .result-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; }
    .shipment-item { padding: 1rem; background: white; border-radius: 8px; border-left: 4px solid var(--primary-green); margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; }
    .error-item { padding: 1rem; background: #fef2f2; border-radius: 8px; border-left: 4px solid #ef4444; margin-bottom: 0.5rem; color: #991b1b; font-size: 0.9rem; }
    .stat-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 800; font-size: 0.9rem; }
    .stat-success { background: #f0fdf4; color: #16a34a; }
    .stat-error { background: #fef2f2; color: #ef4444; }
    .progress-bar { width: 100%; height: 6px; background: #f1f5f9; border-radius: 4px; overflow: hidden; margin-top: 1rem; }
    .progress-fill { height: 100%; background: var(--primary-green); transition: width 0.3s; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Bulk Shipment Import</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Import multiple shipments at once using CSV or Excel files.</p>
        </div>
        <a href="{{ route('admin.shipments.bulk.template') }}" class="btn-secondary" download>
            <i class="fas fa-download"></i> Download Template
        </a>
    </div>
</div>

<!-- Upload Section -->
<div class="import-card">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem;">
        <i class="fas fa-cloud-upload-alt" style="color: var(--primary-green);"></i> Upload File
    </h2>

    <form id="uploadForm" action="{{ route('admin.shipments.bulk.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-file-excel" style="font-size: 2.5rem; color: var(--primary-green); margin-bottom: 0.5rem;"></i>
            <p style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">
                Drag and drop your file here
            </p>
            <p style="font-size: 0.85rem; color: var(--text-gray); margin-bottom: 1rem;">
                or click to select CSV or Excel file (max 5MB)
            </p>
            <input type="file" id="fileInput" name="file" class="file-input" accept=".csv,.xlsx,.xls" onchange="updateFileName()" required>
            <p id="fileName" style="font-size: 0.8rem; color: #94a3b8; font-weight: 700; margin-top: 0.5rem;"></p>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn-primary" id="submitBtn" disabled>
                <i class="fas fa-arrow-up"></i> Import Shipments
            </button>
            <button type="button" class="btn-secondary" onclick="clearFile()">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </form>
</div>

<!-- Instructions -->
<div class="import-card">
    <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">File Format Requirements</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary-green); margin-bottom: 0.5rem;">Required Fields</h4>
            <ul style="list-style: none; padding: 0; font-size: 0.85rem; color: #475569; line-height: 1.8;">
                <li>✓ Client Name</li>
                <li>✓ Tracking Number</li>
                <li>✓ Origin</li>
                <li>✓ Destination</li>
                <li style="color: #94a3b8; margin-top: 0.5rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem;">Optional Fields:</li>
                <li>○ Serial Number</li>
                <li>○ Cargo Type</li>
                <li>○ Weight (KG)</li>
                <li>○ Quantity</li>
                <li>○ Status</li>
                <li>○ Cost (ZMW)</li>
                <li>○ Estimated Delivery (DD/MM/YYYY)</li>
                <li>○ Driver</li>
                <li>○ Vehicle Registration</li>
                <li>○ Cargo Images</li>
            </ul>
        </div>
        <div>
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary-green); margin-bottom: 0.5rem;">Column Sequence</h4>
            <table style="font-size: 0.75rem; width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                    <td style="padding: 0.4rem 0.5rem; color: #94a3b8; font-weight: 700;">Col</td>
                    <td style="padding: 0.4rem 0.5rem; color: #94a3b8; font-weight: 700;">Field</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">1</td><td style="padding: 0.4rem 0.5rem;"><strong>Client Name</strong></td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">2</td><td style="padding: 0.4rem 0.5rem;"><strong>Tracking Number</strong></td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">3</td><td style="padding: 0.4rem 0.5rem;">Serial Number</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">4</td><td style="padding: 0.4rem 0.5rem;"><strong>Origin</strong></td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">5</td><td style="padding: 0.4rem 0.5rem;"><strong>Destination</strong></td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">6</td><td style="padding: 0.4rem 0.5rem;">Cargo Type</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">7</td><td style="padding: 0.4rem 0.5rem;">Weight (KG)</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">8</td><td style="padding: 0.4rem 0.5rem;">Quantity</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">9</td><td style="padding: 0.4rem 0.5rem;">Status</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">10</td><td style="padding: 0.4rem 0.5rem;">Cost (ZMW)</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">11</td><td style="padding: 0.4rem 0.5rem;">Est. Delivery</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">12</td><td style="padding: 0.4rem 0.5rem;">Driver</td></tr>
                <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 0.4rem 0.5rem;">13</td><td style="padding: 0.4rem 0.5rem;">Vehicle Reg</td></tr>
                <tr><td style="padding: 0.4rem 0.5rem;">14</td><td style="padding: 0.4rem 0.5rem;">Images</td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Results Section -->
@if(session('import_results'))
@php $results = session('import_results'); @endphp
<div class="results-section">
    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Import Results</h2>

    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-badge stat-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ $results['success'] }} Created</span>
        </div>
        <div class="stat-badge stat-error">
            <i class="fas fa-times-circle"></i>
            <span>{{ $results['failed'] }} Failed</span>
        </div>
    </div>

    @if($results['success'] > 0)
    <div class="result-success">
        <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; color: #16a34a;">
            <i class="fas fa-check-circle"></i> Successfully Created Shipments
        </h3>
        @foreach($results['shipments'] as $shipment)
        <div class="shipment-item">
            <div>
                <div style="font-weight: 700; color: var(--text-dark);">{{ $shipment['tracking'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-gray);">{{ $shipment['client'] }}</div>
                <div style="font-size: 0.8rem; color: #94a3b8;">{{ $shipment['origin'] }} → {{ $shipment['destination'] }}</div>
            </div>
            <i class="fas fa-check" style="color: #16a34a; font-size: 1.25rem;"></i>
        </div>
        @endforeach
    </div>
    @endif

    @if($results['failed'] > 0)
    <div class="result-error">
        <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; color: #ef4444;">
            <i class="fas fa-exclamation-circle"></i> Import Errors
        </h3>
        @foreach($results['errors'] as $error)
        <div class="error-item">{{ $error }}</div>
        @endforeach
    </div>
    @endif
</div>
@endif

@if($errors->any())
<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
    <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; color: #ef4444;">
        <i class="fas fa-exclamation-triangle"></i> Validation Errors
    </h3>
    @foreach($errors->all() as $error)
    <div style="padding: 0.75rem 0; color: #991b1b; font-size: 0.9rem;">{{ $error }}</div>
    @endforeach
</div>
@endif

<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        updateFileName();
    });

    function updateFileName() {
        const file = fileInput.files[0];
        if (file) {
            document.getElementById('fileName').textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
            submitBtn.disabled = false;
        }
    }

    function clearFile() {
        fileInput.value = '';
        document.getElementById('fileName').textContent = '';
        submitBtn.disabled = true;
    }

    uploadForm.addEventListener('submit', (e) => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
    });
</script>
@endsection
