<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-image" style="color: #3b82f6;"></i> Hero Section</div>
    <div class="row-grid">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Hero Title</label>
            <input type="text" name="sections[hero_title]" class="form-control" value="{{ $sections['hero_title'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Hero Subtitle</label>
            <input type="text" name="sections[hero_subtitle]" class="form-control" value="{{ $sections['hero_subtitle'] ?? '' }}">
        </div>
    </div>
    <div class="row-grid" style="margin-top: 1rem;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">CTA Button Text</label>
            <input type="text" name="sections[hero_cta_text]" class="form-control" value="{{ $sections['hero_cta_text'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">CTA Button Link</label>
            <input type="text" name="sections[hero_cta_link]" class="form-control" value="{{ $sections['hero_cta_link'] ?? '' }}">
        </div>
    </div>
    <div style="margin-top: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Hero Image</label>
        <div class="image-upload-wrapper">
            <div class="image-preview" id="hero_image_preview">
                @if(!empty($sections['hero_image']))
                <img src="{{ $sections['hero_image'] }}" alt="Hero">
                @else
                <div class="placeholder">No image</div>
                @endif
            </div>
            <div>
                <input type="hidden" name="sections[hero_image]" id="hero_image_url" value="{{ $sections['hero_image'] ?? '' }}">
                <input type="file" accept="image/*" onchange="handleImageUpload(this, 'hero_image_preview', 'hero_image_url')" style="font-size: 0.8rem;">
                <span class="upload-progress"></span>
            </div>
        </div>
    </div>
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-star" style="color: #f59e0b;"></i> Features (3 cards)</div>
    @for($i = 0; $i < 3; $i++)
    <div class="feature-card" style="margin-bottom: 1rem;">
        <div class="row-grid-3">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Icon (fa-...)</label>
                <input type="text" name="sections[features][{{ $i }}][icon]" class="form-control" value="{{ $sections['features'][$i]['icon'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Title</label>
                <input type="text" name="sections[features][{{ $i }}][title]" class="form-control" value="{{ $sections['features'][$i]['title'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Text</label>
                <input type="text" name="sections[features][{{ $i }}][text]" class="form-control" value="{{ $sections['features'][$i]['text'] ?? '' }}">
            </div>
        </div>
    </div>
    @endfor
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-chart-bar" style="color: #16a34a;"></i> Stats (3 numbers)</div>
    @for($i = 0; $i < 3; $i++)
    <div class="stat-row">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Number {{ $i + 1 }}</label>
            <input type="text" name="sections[stats][{{ $i }}][number]" class="form-control" value="{{ $sections['stats'][$i]['number'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Label {{ $i + 1 }}</label>
            <input type="text" name="sections[stats][{{ $i }}][label]" class="form-control" value="{{ $sections['stats'][$i]['label'] ?? '' }}">
        </div>
    </div>
    @endfor
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-building" style="color: #8b5cf6;"></i> About Section</div>
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">About Title</label>
        <input type="text" name="sections[about_title]" class="form-control" value="{{ $sections['about_title'] ?? '' }}">
    </div>
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">About Text</label>
        <textarea name="sections[about_text]" class="rich-editor">{{ $sections['about_text'] ?? '' }}</textarea>
    </div>
    <div>
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">About Image</label>
        <div class="image-upload-wrapper">
            <div class="image-preview" id="about_image_preview">
                @if(!empty($sections['about_image']))
                <img src="{{ $sections['about_image'] }}" alt="About">
                @else
                <div class="placeholder">No image</div>
                @endif
            </div>
            <div>
                <input type="hidden" name="sections[about_image]" id="about_image_url" value="{{ $sections['about_image'] ?? '' }}">
                <input type="file" accept="image/*" onchange="handleImageUpload(this, 'about_image_preview', 'about_image_url')" style="font-size: 0.8rem;">
                <span class="upload-progress"></span>
            </div>
        </div>
    </div>
</div>
