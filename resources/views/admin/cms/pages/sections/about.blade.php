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
    <div class="section-header"><i class="fas fa-align-left" style="color: #16a34a;"></i> Content</div>
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Main Content</label>
        <textarea name="sections[content]" class="rich-editor">{{ $sections['content'] ?? '' }}</textarea>
    </div>
    <div class="row-grid">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Mission</label>
            <input type="text" name="sections[mission]" class="form-control" value="{{ $sections['mission'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Vision</label>
            <input type="text" name="sections[vision]" class="form-control" value="{{ $sections['vision'] ?? '' }}">
        </div>
    </div>
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-users" style="color: #f59e0b;"></i> Team Members</div>
    @for($i = 0; $i < 2; $i++)
    <div class="feature-card" style="margin-bottom: 1rem;">
        <div class="row-grid-3">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Name</label>
                <input type="text" name="sections[team][{{ $i }}][name]" class="form-control" value="{{ $sections['team'][$i]['name'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Role</label>
                <input type="text" name="sections[team][{{ $i }}][role]" class="form-control" value="{{ $sections['team'][$i]['role'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Image</label>
                <div class="image-upload-wrapper">
                    <div class="image-preview" id="team_{{ $i }}_image_preview">
                        @if(!empty($sections['team'][$i]['image']))
                        <img src="{{ $sections['team'][$i]['image'] }}" alt="Team">
                        @else
                        <div class="placeholder">No image</div>
                        @endif
                    </div>
                    <div>
                        <input type="hidden" name="sections[team][{{ $i }}][image]" id="team_{{ $i }}_image_url" value="{{ $sections['team'][$i]['image'] ?? '' }}">
                        <input type="file" accept="image/*" onchange="handleImageUpload(this, 'team_{{ $i }}_image_preview', 'team_{{ $i }}_image_url')" style="font-size: 0.8rem;">
                        <span class="upload-progress"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endfor
</div>
