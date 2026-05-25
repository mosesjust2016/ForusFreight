<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-heading" style="color: #3b82f6;"></i> Header</div>
    <div class="row-grid">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Page Title</label>
            <input type="text" name="sections[title]" class="form-control" value="{{ $sections['title'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Subtitle</label>
            <input type="text" name="sections[subtitle]" class="form-control" value="{{ $sections['subtitle'] ?? '' }}">
        </div>
    </div>
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-list" style="color: #16a34a;"></i> Services (6 items)</div>
    @for($i = 0; $i < 6; $i++)
    <div class="feature-card" style="margin-bottom: 1rem;">
        <div class="row-grid-3">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Icon (fa-...)</label>
                <input type="text" name="sections[services][{{ $i }}][icon]" class="form-control" value="{{ $sections['services'][$i]['icon'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Service Title</label>
                <input type="text" name="sections[services][{{ $i }}][title]" class="form-control" value="{{ $sections['services'][$i]['title'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Description</label>
                <input type="text" name="sections[services][{{ $i }}][description]" class="form-control" value="{{ $sections['services'][$i]['description'] ?? '' }}">
            </div>
        </div>
    </div>
    @endfor
</div>
