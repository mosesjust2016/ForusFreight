@extends('layouts.guest-shipment')

@section('title', 'New Shipment Request - Forus Freight')

@section('styles')
<style>
    .form-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 32px;
        padding: 3rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        margin-top: 1rem;
    }

    .form-section {
        margin-bottom: 3rem;
    }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f8fafc;
    }

    .form-section-header i {
        width: 40px;
        height: 40px;
        background: #e8f5e9;
        color: #4caf50;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .form-section-header h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
    }

    .input-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .form-group label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s;
        background: #f8fafc;
        color: #1e293b;
    }

    .form-control:focus {
        outline: none;
        border-color: #007f7f;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.05);
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
        border-color: #007f7f;
        background: #f0f9f9;
    }

    .btn-submit {
        background: #007f7f;
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 16px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 20px rgba(0, 127, 127, 0.2);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(0, 127, 127, 0.3);
        background: #006666;
    }

    @media (max-width: 640px) {
        .input-grid { grid-template-columns: 1fr; }
        .form-card { padding: 1.5rem; }
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">New Shipment</h1>
        <p style="color: #64748b; font-weight: 500; margin-top: 0.5rem;">Fill in the details to request a new cargo transit.</p>
    </div>
    <a href="{{ route('client.shipments') }}" style="text-decoration: none; color: #64748b; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="form-container">
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 1.25rem; border-radius: 16px; margin-bottom: 2rem;">
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
            
            <!-- Step 1: Client Information -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user"></i>
                    <h3>Client Information</h3>
                </div>
                <div class="input-grid">
                    <div class="form-group">
                        <label>Client Name</label>
                        <input type="text" name="client_name" class="form-control" placeholder="e.g. ADRIAN CHUNGA" required>
                    </div>
                    <div class="form-group">
                        <label>Client Phone Number</label>
                        <input type="tel" name="client_phone" class="form-control" placeholder="e.g. 260970026344" required>
                    </div>
                </div>
            </div>

            <!-- Step 2: Route & Port Details -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-route"></i>
                    <h3>Route & Port Details</h3>
                </div>
                <div class="input-grid">
                    <div class="form-group">
                        <label>Origin Country</label>
                        <select name="origin_country" id="origin_country" class="form-control" onchange="updateCities('origin')" required>
                            <option value="" disabled selected>Select country...</option>
                            <option value="China">China</option>
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
                            <option value="">Select country first...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Port of Origin</label>
                        <input type="text" name="port_of_origin" class="form-control" placeholder="e.g. GUANGZHOU PORT">
                    </div>
                    <div class="form-group">
                        <label>Destination Country</label>
                        <select name="destination_country" id="destination_country" class="form-control" onchange="updateCities('destination')" required>
                            <option value="" disabled selected>Select country...</option>
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
                            <option value="">Select country first...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Port Destination</label>
                        <input type="text" name="port_destination" class="form-control" placeholder="e.g. PORT OF BEIRA (MOZAMBIQUE)">
                    </div>
                </div>
            </div>

            <!-- Step 3: Shipment Details -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-box"></i>
                    <h3>Shipment Details</h3>
                </div>
                <div class="input-grid">
                    <div class="form-group">
                        <label>Serial No *</label>
                        <input type="text" name="serial_no" class="form-control" placeholder="e.g. RS.26052049" required>
                    </div>
                    <div class="form-group">
                        <label>Tracking Number</label>
                        <input type="text" name="tracking_number" class="form-control" placeholder="e.g. 610080707216">
                    </div>
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. ZMFFL">
                    </div>
                    <div class="form-group">
                        <label>Service Type</label>
                        <select name="service_type" class="form-control" required>
                            <option value="IMPORT">IMPORT</option>
                            <option value="EXPORT">EXPORT</option>
                            <option value="Road Freight">Road Freight</option>
                            <option value="Air Freight">Air Freight</option>
                            <option value="Sea Freight">Sea Freight</option>
                            <option value="Express Delivery">Express Delivery</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shipping Method</label>
                        <select name="shipping_method" class="form-control" required>
                            <option value="SEA">SEA</option>
                            <option value="AIR">AIR</option>
                            <option value="ROAD">ROAD</option>
                            <option value="RAIL">RAIL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Initial Status</label>
                        <select name="initial_status" class="form-control" required>
                            <option value="ORDERED">ORDERED</option>
                            <option value="IN TRANSIT">IN TRANSIT</option>
                            <option value="AT PORT">AT PORT</option>
                            <option value="CUSTOMS CLEARANCE">CUSTOMS CLEARANCE</option>
                            <option value="DELIVERED">DELIVERED</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date of Load</label>
                        <input type="date" name="date_of_load" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>ETA (Estimated Delivery)</label>
                        <input type="date" name="estimated_delivery" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- Step 4: Cargo Details -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-weight-hanging"></i>
                    <h3>Cargo Details</h3>
                </div>
                <div class="input-grid">
                    <div class="form-group">
                        <label>Description of Goods</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. Bathroom Supplies" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No of Parcels</label>
                        <input type="number" name="no_of_parcels" class="form-control" placeholder="e.g. 3" required>
                    </div>
                    <div class="form-group">
                        <label>CBM / Volume (m³)</label>
                        <input type="number" name="cbm_volume" class="form-control" step="0.01" placeholder="e.g. 0.6" required>
                    </div>
                    <div class="form-group">
                        <label>KGS / Gross Weight</label>
                        <input type="number" name="gross_weight" class="form-control" step="0.01" placeholder="e.g. 136" required>
                    </div>
                    <div class="form-group">
                        <label>Cost (ZMW)</label>
                        <input type="number" name="cost" class="form-control" step="0.01" placeholder="e.g. 15000.00">
                    </div>
                </div>
            </div>

            <!-- Step 5: Documents -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Documentation</h3>
                </div>
                <div class="upload-zone" id="dropZone">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Upload Shipping Docs</h4>
                    <p style="font-size: 0.85rem; color: #64748b;">Drag & drop files or click to browse</p>
                    <input type="file" name="documents[]" id="fileInput" style="display: none;" multiple>
                </div>
            </div>

            <!-- Step 6: Shipment Images -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-camera"></i>
                    <h3>Shipment Images</h3>
                </div>
                <div class="upload-zone" id="imageDropZone">
                    <i class="fas fa-images" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Upload Cargo Images</h4>
                    <p style="font-size: 0.85rem; color: #64748b;">Upload photos of the cargo, packaging, or labels</p>
                    <input type="file" name="images[]" id="imageInput" style="display: none;" multiple accept="image/*">
                </div>
                <div id="imagePreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1.5rem;"></div>
            </div>

            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 2rem; margin-top: 2rem;">
                <button type="submit" class="btn-submit">
                    Send Request <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const cityData = {
        'China': ['Guangzhou', 'Shenzhen', 'Shanghai', 'Ningbo', 'Qingdao', 'Xiamen', 'Yiwu'],
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
            const otherOption = document.createElement('option');
            otherOption.value = 'Other';
            otherOption.textContent = 'Other';
            citySelect.appendChild(otherOption);
        }
    }

    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    dropZone.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', () => {
        if(fileInput.files.length > 0) {
            dropZone.style.borderColor = '#007f7f';
            dropZone.querySelector('p').textContent = fileInput.files.length + ' files selected';
        }
    });

    const imageDropZone = document.getElementById('imageDropZone');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    imageDropZone.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', (e) => {
        imagePreview.innerHTML = '';
        if (imageInput.files.length > 0) {
            imageDropZone.style.borderColor = '#007f7f';
            Array.from(imageInput.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.style.cssText = 'position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);';
                    div.innerHTML = `
                        <img src="${e.target.result}" style="width: 100%; height: 150px; object-fit: cover;">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); color: white; padding: 0.5rem; font-size: 0.75rem; font-weight: 600;">
                            ${file.name}
                        </div>
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
