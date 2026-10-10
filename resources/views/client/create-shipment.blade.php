@extends(Auth::check() ? 'layouts.dashboard' : 'layouts.guest-shipment')

@section('title', 'New shipment - Forus Freight')

@section('styles')
<style>
    .ns {
        /* Same tokens as partials/ui-base-styles so this page also renders in
           the guest and staff layouts, which don't include that partial. */
        --ink: #0d1b20;
        --ink-2: #33444a;
        --muted: #56666c;
        --teal: #007f7f;
        --teal-deep: #005e5e;
        --orange: #ff6200;
        --line: rgba(13, 27, 32, 0.12);
        --line-strong: rgba(13, 27, 32, 0.22);
        --display: 'Archivo', 'Inter', sans-serif;
        --mono: 'IBM Plex Mono', ui-monospace, monospace;

        max-width: 760px;
    }

    .ns-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 40px;
        margin-bottom: 0.75rem;
        color: var(--ink-2);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .ns-back:hover { color: var(--ink); }

    .ns h1 {
        font-family: var(--display);
        font-stretch: 106%;
        font-weight: 800;
        font-size: clamp(1.7rem, 3vw, 2.2rem);
        letter-spacing: -0.02em;
    }

    .ns-sub {
        margin-top: 0.35rem;
        color: var(--ink-2);
    }

    .ns-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 1.5rem;
        margin-top: 1.25rem;
    }

    .ns-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.1rem;
    }

    .ns-card-head h2 {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .ns-tag {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--teal-deep);
        background: #e6f6f4;
        border-radius: 999px;
        padding: 0.2rem 0.65rem;
        white-space: nowrap;
    }

    .ns-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.1rem 1.25rem;
    }

    .ns-field {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        min-width: 0;
    }

    .ns-field > label,
    .ns-legend {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .ns-req { color: var(--orange); }

    .ns-opt {
        font-weight: 600;
        letter-spacing: 0;
        text-transform: none;
        color: var(--muted);
    }

    .ns-in {
        width: 100%;
        min-height: 46px;
        padding: 0.7rem 1rem;
        border: 1.5px solid var(--line-strong);
        border-radius: 12px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 0.95rem;
        font-weight: 600;
        transition: border-color 0.15s ease;
    }

    .ns-in:focus {
        outline: none;
        border-color: var(--teal);
    }

    .ns-in[aria-invalid="true"] { border-color: #dc2626; }

    select.ns-in { cursor: pointer; }

    .ns-hint {
        font-size: 0.78rem;
        color: var(--muted);
        line-height: 1.5;
    }

    .ns-span { grid-column: 1 / -1; }

    /* Shipping method chips */
    .ns-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .ns-chip { position: relative; }

    .ns-chip input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
    }

    .ns-chip span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 1.2rem;
        border: 1.5px solid var(--line-strong);
        border-radius: 999px;
        background: #fff;
        color: var(--ink);
        font-size: 0.9rem;
        font-weight: 600;
        pointer-events: none;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .ns-chip input:checked + span {
        background: var(--ink);
        border-color: var(--ink);
        color: #fff;
    }

    .ns-chip input:focus-visible + span {
        outline: 3px solid var(--orange);
        outline-offset: 2px;
    }

    /* Parcel-code note */
    .ns-note {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        margin-top: 1.25rem;
        padding: 0.8rem 0.95rem;
        background: #faf9f5;
        border: 1px solid var(--line);
        border-radius: 12px;
        font-size: 0.85rem;
        color: var(--ink-2);
        line-height: 1.55;
    }

    .ns-note i { color: var(--teal); margin-top: 0.25rem; }

    .ns-code {
        font-family: var(--mono);
        color: #b54400;
        font-weight: 500;
    }

    /* Photo upload */
    .ns-drop {
        display: grid;
        justify-items: center;
        gap: 0.2rem;
        padding: 1.5rem 1rem;
        border: 1.5px dashed var(--line-strong);
        border-radius: 14px;
        background: #fafaf7;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .ns-drop:hover,
    .ns-drop.is-on {
        border-color: var(--teal);
        background: #f0fdfa;
    }

    .ns-drop i { font-size: 1.35rem; color: var(--muted); }
    .ns-drop strong { font-size: 0.9rem; }
    .ns-drop span { font-size: 0.78rem; color: var(--muted); }

    .ns-previews {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-top: 0.85rem;
    }

    .ns-previews:empty { display: none; }

    .ns-prev {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(13, 27, 32, 0.12);
    }

    .ns-prev img {
        display: block;
        width: 100%;
        height: 110px;
        object-fit: cover;
    }

    .ns-prev button {
        position: absolute;
        top: 0.35rem;
        right: 0.35rem;
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 50%;
        background: rgba(13, 27, 32, 0.72);
        color: #fff;
        cursor: pointer;
    }

    .ns-prev em {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 0.3rem 0.5rem;
        background: rgba(13, 27, 32, 0.65);
        color: #fff;
        font-size: 0.7rem;
        font-style: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ns-err {
        color: #b91c1c;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .ns-err:empty { display: none; }

    /* Optional extras */
    .ns-more {
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--line);
    }

    .ns-more summary {
        width: fit-content;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--teal-deep);
    }

    .ns-more[open] summary { margin-bottom: 0.75rem; }

    /* Submit */
    .ns-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .ns-send {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        min-height: 50px;
        padding: 0 1.75rem;
        border: none;
        border-radius: 12px;
        background: var(--orange);
        color: var(--ink);
        font: inherit;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .ns-send:hover { background: #ff7a26; }

    .ns-foot p {
        font-size: 0.85rem;
        color: var(--muted);
        max-width: 44ch;
    }

    /* Validation errors — the portal shell shows these too, but the guest
       layout has nowhere to render them. */
    .ns-alert {
        display: flex;
        gap: 0.7rem;
        margin-top: 1.25rem;
        padding: 0.9rem 1.1rem;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .ns-alert ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    @media (max-width: 640px) {
        .ns-grid { grid-template-columns: 1fr; }
        .ns-card { padding: 1.25rem 1.1rem; }
        .ns-send { width: 100%; justify-content: center; }
        .ns-foot p { max-width: none; }
    }
</style>
@endsection

@section('content')
@php
    $codeValue = old('code', $parcelCode);
    $goods = ['Bathroom Supplies', 'Electronics & Appliances', 'Clothing & Textiles', 'Cosmetics & Beauty Products', 'Automotive & Spare Parts', 'Home & Furniture', 'Industrial & Machinery', 'Construction Materials', 'Groceries & Food Items', 'Medical & Pharmaceutical', 'Personal Effects & Luggage'];

    $oldGoods = old('description');
    $oldGoodsOther = old('description_other');
    // Anything that isn't one of the known categories was typed by the
    // customer, so bring it back into the "Other" box.
    $goodsIsOther = $oldGoods !== null && $oldGoods !== '' && ! in_array($oldGoods, $goods, true) && $oldGoods !== 'Other (specify below)';
    $goodsOtherValue = $oldGoodsOther ?: ($goodsIsOther ? $oldGoods : '');

    $selectedMethod = old('shipping_method', $defaults['shipping_method']);
@endphp

<div class="ns">
    @auth
        <a href="{{ route('client.shipments') }}" class="ns-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Shipments</a>
    @endauth

    <h1>New shipment</h1>
    <p class="ns-sub">Tell us what’s coming — we confirm dates, weight and pricing once your goods reach our warehouse.</p>

    @guest
        @if($errors->any())
            <div class="ns-alert" role="alert">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endguest

    <form action="{{ route('client.shipments.store') }}" method="POST" enctype="multipart/form-data" id="shipmentForm" data-loading-label="Sending request…">
        @csrf
        <input type="hidden" name="code" value="{{ $codeValue }}">
        {{-- Derived from the origin country: China orders are imports, anything
             else an export (the server used to ask customers to pick this). --}}
        <input type="hidden" name="service_type" id="nsServiceType" value="{{ old('service_type', 'IMPORT') }}">

        <section class="ns-card" aria-labelledby="nsDetailsH">
            <div class="ns-card-head">
                <h2 id="nsDetailsH">Your details</h2>
                @if($account)
                    <span class="ns-tag">From your account</span>
                @endif
            </div>
            <div class="ns-grid">
                <div class="ns-field">
                    <label for="client_name">Name <span class="ns-req" aria-hidden="true">*</span></label>
                    <input id="client_name" name="client_name" type="text" class="ns-in" value="{{ old('client_name', $account?->name ?? '') }}" placeholder="e.g. Adrian Chunga" autocomplete="name" required>
                </div>
                <div class="ns-field">
                    <label for="client_phone">Phone <span class="ns-req" aria-hidden="true">*</span></label>
                    <input id="client_phone" name="client_phone" type="tel" class="ns-in" value="{{ old('client_phone', $account?->phone ?? '') }}" placeholder="e.g. 260970026344" autocomplete="tel" required>
                    <span class="ns-hint">How we match parcels to you and reach you about delivery.</span>
                </div>
            </div>
        </section>

        <section class="ns-card" aria-labelledby="nsRouteH">
            <div class="ns-card-head">
                <h2 id="nsRouteH">Route</h2>
            </div>
            <div class="ns-grid">
                <div class="ns-field">
                    <label for="origin_country">Shipping from <span class="ns-req" aria-hidden="true">*</span></label>
                    <select id="origin_country" name="origin_country" class="ns-in" required>
                        @foreach($originCountries as $country)
                            <option value="{{ $country }}" @selected(old('origin_country', $defaults['origin_country']) === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ns-field">
                    <label for="origin_city">From city <span class="ns-req" aria-hidden="true">*</span></label>
                    <select id="origin_city" name="origin_city" class="ns-in" required data-value="{{ old('origin_city', $defaults['origin_city']) }}"><option value="" disabled>Select city…</option></select>
                    <input type="text" name="origin_city_other" id="origin_city_other" class="ns-in" value="{{ old('origin_city_other') }}" placeholder="Which city?" style="display: none;">
                </div>
                <div class="ns-field">
                    <label for="destination_country">Deliver to <span class="ns-req" aria-hidden="true">*</span></label>
                    <select id="destination_country" name="destination_country" class="ns-in" required>
                        @foreach($destinationCountries as $country)
                            <option value="{{ $country }}" @selected(old('destination_country', $defaults['destination_country']) === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ns-field">
                    <label for="destination_city">Delivery city <span class="ns-req" aria-hidden="true">*</span></label>
                    <select id="destination_city" name="destination_city" class="ns-in" required data-value="{{ old('destination_city', $defaults['destination_city']) }}"><option value="" disabled>Select city…</option></select>
                    <input type="text" name="destination_city_other" id="destination_city_other" class="ns-in" value="{{ old('destination_city_other') }}" placeholder="Which city?" style="display: none;">
                </div>
            </div>

            <div class="ns-field" style="margin-top: 1.25rem;">
                <span class="ns-legend" id="nsMethodLabel">How should it travel? <span class="ns-req" aria-hidden="true">*</span></span>
                <div class="ns-chips" role="radiogroup" aria-labelledby="nsMethodLabel">
                    @foreach(['SEA' => 'Sea', 'AIR' => 'Air', 'ROAD' => 'Road', 'RAIL' => 'Rail'] as $value => $label)
                        <label class="ns-chip">
                            <input type="radio" name="shipping_method" value="{{ $value }}" @checked($selectedMethod === $value) required>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <span class="ns-hint">Sea is the usual choice — slowest but cheapest. Air is fastest.</span>
            </div>

            <p class="ns-note">
                <i class="fas fa-tag" aria-hidden="true"></i>
                <span>Parcel code <strong class="ns-code">{{ $codeValue }}</strong> — write it on each parcel with your name and phone so we can match it to you.</span>
            </p>
        </section>

        <section class="ns-card" aria-labelledby="nsCargoH">
            <div class="ns-card-head">
                <h2 id="nsCargoH">Cargo</h2>
            </div>
            <div class="ns-grid">
                <div class="ns-field">
                    <label for="description">What are you sending? <span class="ns-req" aria-hidden="true">*</span></label>
                    <select id="description" name="description" class="ns-in" required>
                        <option value="" disabled @selected(!$oldGoods)>Choose goods type…</option>
                        @foreach($goods as $category)
                            <option value="{{ $category }}" @selected(!$goodsIsOther && $oldGoods === $category)>{{ $category }}</option>
                        @endforeach
                        <option value="Other (specify below)" @selected($oldGoods === 'Other (specify below)' || $goodsIsOther)>Other (specify below)</option>
                    </select>
                    <input type="text" name="description_other" id="description_other" class="ns-in" value="{{ $goodsOtherValue }}" placeholder="What are they?" style="display: {{ $goodsIsOther ? 'block' : 'none' }};">
                </div>
                <div class="ns-field">
                    <label for="no_of_parcels">How many parcels? <span class="ns-req" aria-hidden="true">*</span></label>
                    <input id="no_of_parcels" name="no_of_parcels" type="number" class="ns-in" min="1" step="1" value="{{ old('no_of_parcels', $defaults['no_of_parcels']) }}" required>
                </div>

                <div class="ns-field ns-span">
                    <label for="nsImages">Photos <span class="ns-opt">(optional)</span></label>
                    <div class="ns-drop" id="nsDrop">
                        <i class="fas fa-images" aria-hidden="true"></i>
                        <strong>Add photos of your goods</strong>
                        <span>JPG, PNG or WebP · up to 5 MB each</span>
                    </div>
                    <input type="file" name="images[]" id="nsImages" multiple accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                    <div class="ns-previews" id="nsPreviews"></div>
                    <div class="ns-err" id="nsUploadErr"></div>
                </div>
            </div>

            <details class="ns-more">
                <summary>Already have a courier tracking number?</summary>
                <input type="text" name="tracking_number" id="tracking_number" class="ns-in" value="{{ old('tracking_number') }}" placeholder="e.g. 610080707216">
                <span class="ns-hint">Optional — paste it if your courier has already issued one.</span>
            </details>
        </section>

        <div class="ns-foot">
            <button type="submit" class="ns-send">Send request <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
            <p>Every request is reviewed by our team before anything is booked.</p>
        </div>
    </form>
</div>

<script>
    const cityData = @json($cities);

    function fillCities(type) {
        const countryEl = document.getElementById(type + '_country');
        const cityEl = document.getElementById(type + '_city');
        const wanted = cityEl.dataset.value || '';
        const list = (cityData[countryEl.value] || []).slice();

        // Keep a city we already know about (from the account's last shipment
        // or a validation round-trip) even when it isn't in the preset list.
        if (wanted && wanted !== 'Other' && list.indexOf(wanted) === -1) list.unshift(wanted);
        list.push('Other');

        cityEl.innerHTML = '';
        cityEl.appendChild(new Option('Select city…', ''));
        list.forEach(city => cityEl.appendChild(new Option(city, city)));
        cityEl.value = wanted;

        syncCityOther(type);
    }

    function syncCityOther(type) {
        const cityEl = document.getElementById(type + '_city');
        const otherEl = document.getElementById(type + '_city_other');
        otherEl.style.display = cityEl.value === 'Other' ? 'block' : 'none';
    }

    ['origin', 'destination'].forEach(function (type) {
        fillCities(type);

        document.getElementById(type + '_country').addEventListener('change', function () {
            document.getElementById(type + '_city').dataset.value = '';
            fillCities(type);
            if (type === 'origin') syncServiceType();
        });

        document.getElementById(type + '_city').addEventListener('change', function () {
            syncCityOther(type);
        });
    });

    /* ── Service type: a hidden field the server used to ask for ── */
    function syncServiceType() {
        document.getElementById('nsServiceType').value =
            document.getElementById('origin_country').value === 'China' ? 'IMPORT' : 'EXPORT';
    }
    syncServiceType();

    /* ── "Other (specify below)" for goods ── */
    const goodsSelect = document.getElementById('description');
    const goodsOther = document.getElementById('description_other');

    function syncGoodsOther() {
        goodsOther.style.display = goodsSelect.value === 'Other (specify below)' ? 'block' : 'none';
    }
    goodsSelect.addEventListener('change', syncGoodsOther);
    syncGoodsOther();

    /* Before the request goes out, swap each "Other" pick for what the
       customer actually typed. Empty answers block the submit with a
       nudge instead of sending "Other" to the server. */
    function resolveOther(getEl, getTextEl, message) {
        if (getEl.value !== 'Other' && getEl.value !== 'Other (specify below)') return true;

        const text = getTextEl.value.trim();
        getTextEl.style.display = 'block';

        if (!text) {
            getTextEl.setAttribute('aria-invalid', 'true');
            getTextEl.setCustomValidity(message);
            getTextEl.reportValidity();
            getTextEl.focus();
            getTextEl.addEventListener('input', function () {
                getTextEl.removeAttribute('aria-invalid');
                getTextEl.setCustomValidity('');
            }, { once: true });
            return false;
        }

        getTextEl.removeAttribute('aria-invalid');
        getTextEl.setCustomValidity('');
        if (!Array.from(getEl.options).some(o => o.value === text)) {
            getEl.appendChild(new Option(text, text));
        }
        getEl.value = text;
        return true;
    }

    document.getElementById('shipmentForm').addEventListener('submit', function (e) {
        const originOk = resolveOther(
            document.getElementById('origin_city'),
            document.getElementById('origin_city_other'),
            'Tell us which city.'
        );
        const destinationOk = resolveOther(
            document.getElementById('destination_city'),
            document.getElementById('destination_city_other'),
            'Tell us which city.'
        );
        const goodsOk = resolveOther(goodsSelect, goodsOther, 'Tell us what you’re sending.');

        if (!originOk || !destinationOk || !goodsOk) e.preventDefault();
    });

    /* ── Photo upload ──
       Client-side only — a fast, friendly first check so people don't wait
       for a round-trip to find out a file is the wrong type or too big.
       This is NOT the real security boundary: the server always re-checks
       and re-encodes every image regardless of what the browser reports
       here, since a script can trivially send whatever it wants. */
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    const MAX_IMAGE_BYTES = 5 * 1024 * 1024;
    let stagedImages = new DataTransfer();

    const dropZone = document.getElementById('nsDrop');
    const imageInput = document.getElementById('nsImages');
    const previews = document.getElementById('nsPreviews');
    const uploadErr = document.getElementById('nsUploadErr');

    dropZone.addEventListener('click', () => imageInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('is-on');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('is-on'));

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('is-on');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });

    imageInput.addEventListener('change', () => {
        const errors = [];
        Array.from(imageInput.files).forEach((file) => {
            if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                errors.push(`"${file.name}" isn't a supported image type (JPG, PNG or WebP only).`);
                return;
            }
            if (file.size > MAX_IMAGE_BYTES) {
                errors.push(`"${file.name}" is ${(file.size / (1024 * 1024)).toFixed(1)} MB — the limit is 5 MB.`);
                return;
            }
            stagedImages.items.add(file);
        });
        uploadErr.textContent = errors.join(' ');
        imageInput.files = stagedImages.files;
        dropZone.classList.toggle('is-on', stagedImages.files.length > 0);
        renderPreviews();
    });

    function renderPreviews() {
        previews.innerHTML = '';
        Array.from(stagedImages.files).forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'ns-prev';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = '';

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.setAttribute('aria-label', 'Remove ' + file.name);
            remove.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';
            remove.addEventListener('click', () => removeStagedImage(index));

            const name = document.createElement('em');
            name.textContent = file.name;

            card.append(img, remove, name);
            previews.appendChild(card);
        });
    }

    function removeStagedImage(index) {
        const next = new DataTransfer();
        Array.from(stagedImages.files).forEach((file, i) => {
            if (i !== index) next.items.add(file);
        });
        stagedImages = next;
        imageInput.files = stagedImages.files;
        dropZone.classList.toggle('is-on', stagedImages.files.length > 0);
        renderPreviews();
    }
</script>
@endsection
