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
    <div class="section-header"><i class="fas fa-file-contract" style="color: #8b5cf6;"></i> Terms Content</div>
    <div>
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Full Content</label>
        <textarea name="sections[content]" class="rich-editor" style="min-height: 500px;">{{ $sections['content'] ?? '' }}</textarea>
    </div>
</div>
