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
    <div class="section-header"><i class="fas fa-address-card" style="color: #16a34a;"></i> Contact Details</div>
    <div class="row-grid-2">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Address</label>
            <input type="text" name="sections[address]" class="form-control" value="{{ $sections['address'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Phone</label>
            <input type="text" name="sections[phone]" class="form-control" value="{{ $sections['phone'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Email</label>
            <input type="text" name="sections[email]" class="form-control" value="{{ $sections['email'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Business Hours</label>
            <input type="text" name="sections[hours]" class="form-control" value="{{ $sections['hours'] ?? '' }}">
        </div>
    </div>
    <div style="margin-top: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Google Maps Embed URL</label>
        <input type="text" name="sections[map_embed]" class="form-control" value="{{ $sections['map_embed'] ?? '' }}" placeholder="https://www.google.com/maps/embed?pb=...">
    </div>
    <div style="margin-top: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Form Title</label>
        <input type="text" name="sections[form_title]" class="form-control" value="{{ $sections['form_title'] ?? '' }}">
    </div>
</div>
