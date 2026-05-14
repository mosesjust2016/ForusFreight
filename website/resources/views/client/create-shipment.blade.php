@extends('layouts.dashboard')

@section('title', 'New Shipment Request - Forus Freight')

@section('styles')
<style>
    .form-container {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 5rem;
    }

    .form-card {
        background: white;
        border-radius: 30px;
        padding: 4rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
        margin-top: 2rem;
    }

    .form-section {
        margin-bottom: 4rem;
    }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
        padding-bottom: 1.25rem;
        border-bottom: 2px solid #f8fafc;
    }

    .form-section-header i {
        width: 45px;
        height: 45px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .form-section-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .input-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .form-group label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-control {
        width: 100%;
        padding: 0.85rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s;
        background: #fcfdfe;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 4px var(--primary-green-light);
    }

    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s;
    }

    .upload-zone:hover {
        border-color: var(--primary-green);
        background: var(--primary-green-light);
    }

    .btn-submit {
        background: var(--primary-green);
        color: white;
        padding: 1.25rem 3rem;
        border: none;
        border-radius: 15px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(76, 175, 80, 0.3);
        background: #3d8b40;
    }

    @media (max-width: 1024px) {
        .input-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 600px) {
        .input-grid { grid-template-columns: 1fr; }
        .form-card { padding: 1.5rem; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">New Shipment Request</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Initiate a new cargo transit by selecting your route and cargo details.</p>
        </div>
        <a href="{{ route('client.shipments') }}" style="text-decoration: none; color: var(--text-gray); font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="form-container">
    @if($errors->any())
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b; font-size: 0.85rem; font-weight: 600;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('client.shipments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Step 1: Logistics -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-map-location-dot"></i>
                    <h3>Route & Logistics</h3>
                </div>
                <div class="input-grid">
                    <div class="form-group">
                        <label>Origin Country</label>
                        <select name="origin_country" id="origin_country" class="form-control" onchange="updateCities('origin')" required>
                            <option value="" disabled selected>Select Origin Country...</option>
                            <option value="Zambia">Zambia (HQ)</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                            <option value="Botswana">Botswana</option>
                            <option value="Namibia">Namibia</option>
                            <option value="DRC">DRC</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Malawi">Malawi</option>
                            <option value="Mozambique">Mozambique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Origin City</label>
                        <select name="origin_city" id="origin_city" class="form-control" required disabled>
                            <option value="">Select Country First...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Destination Country</label>
                        <select name="destination_country" id="destination_country" class="form-control" onchange="updateCities('destination')" required>
                            <option value="" disabled selected>Select Destination Country...</option>
                            <option value="Zambia">Zambia</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                            <option value="Botswana">Botswana</option>
                            <option value="Namibia">Namibia</option>
                            <option value="DRC">DRC</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Malawi">Malawi</option>
                            <option value="Mozambique">Mozambique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Destination City</label>
                        <select name="destination_city" id="destination_city" class="form-control" required disabled>
                            <option value="">Select Country First...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Cargo -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-box-open"></i>
                    <h3>Cargo Information</h3>
                </div>
                <div class="input-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label>Cargo Classification</label>
                        <select name="cargo_type" class="form-control">
                            <option value="General Cargo">General Cargo</option>
                            <option value="Mining Equipment">Mining Equipment</option>
                            <option value="Hazardous Materials">Hazardous Materials</option>
                            <option value="Agricultural Products">Agricultural Products</option>
                            <option value="Fragile / Electronics">Fragile / Electronics</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estimated Weight (KG)</label>
                        <input type="number" name="weight" class="form-control" placeholder="e.g. 2500" value="{{ old('weight') }}">
                    </div>
                </div>
            </div>

            <!-- Step 3: Documents -->
            <div class="form-section" style="margin-bottom: 2rem;">
                <div class="form-section-header">
                    <i class="fas fa-file-shield"></i>
                    <h3>Shipping Documents</h3>
                </div>
                <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-arrow-up" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h4 style="font-weight: 800; margin-bottom: 0.5rem; color: var(--text-dark);">Upload Documentation</h4>
                    <p style="font-size: 0.8rem; color: var(--text-gray); margin-bottom: 0;">Drag & Drop Bill of Lading, Invoices, or Permits</p>
                    <input type="file" name="documents[]" id="fileInput" style="display: none;" multiple>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 2rem; margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #f8fafc;">
                <span style="font-size: 0.8rem; color: var(--text-gray); font-weight: 600;">
                    <i class="fas fa-info-circle"></i> Verification will be handled by regional dispatch.
                </span>
                <button type="submit" class="btn-submit">
                    Submit Request <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const cityData = {
        'Zambia': ['Lusaka', 'Kitwe', 'Ndola', 'Livingstone', 'Chipata', 'Kabwe', 'Solwezi'],
        'South Africa': ['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth', 'East London'],
        'Zimbabwe': ['Harare', 'Bulawayo', 'Mutare', 'Gweru', 'Victoria Falls'],
        'Botswana': ['Gaborone', 'Francistown', 'Maun', 'Lobatse'],
        'Namibia': ['Windhoek', 'Walvis Bay', 'Swakopmund', 'Luderitz'],
        'DRC': ['Lubumbashi', 'Kinshasa', 'Goma', 'Kolwezi'],
        'Tanzania': ['Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma'],
        'Malawi': ['Lilongwe', 'Blantyre', 'Mzuzu', 'Zomba'],
        'Mozambique': ['Maputo', 'Beira', 'Nampula', 'Tete']
    };

    function updateCities(type) {
        const countrySelect = document.getElementById(type + '_country');
        const citySelect = document.getElementById(type + '_city');
        const selectedCountry = countrySelect.value;

        citySelect.innerHTML = '<option value="" disabled selected>Select City...</option>';
        citySelect.disabled = false;

        if (cityData[selectedCountry]) {
            cityData[selectedCountry].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
            // Add "Other" option
            const otherOption = document.createElement('option');
            otherOption.value = 'Other';
            otherOption.textContent = 'Other (Specify in notes)';
            citySelect.appendChild(otherOption);
        }
    }
</script>
@endsection
