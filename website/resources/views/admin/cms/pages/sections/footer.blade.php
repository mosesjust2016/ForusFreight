<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-building" style="color: #3b82f6;"></i> Company Info</div>
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Description</label>
        <textarea name="sections[description]" class="form-control" rows="3">{{ $sections['description'] ?? '' }}</textarea>
    </div>
    <div class="row-grid-4">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Facebook URL</label>
            <input type="text" name="sections[social_facebook]" class="form-control" value="{{ $sections['social_facebook'] ?? '#' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Twitter URL</label>
            <input type="text" name="sections[social_twitter]" class="form-control" value="{{ $sections['social_twitter'] ?? '#' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">LinkedIn URL</label>
            <input type="text" name="sections[social_linkedin]" class="form-control" value="{{ $sections['social_linkedin'] ?? '#' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Instagram URL</label>
            <input type="text" name="sections[social_instagram]" class="form-control" value="{{ $sections['social_instagram'] ?? '#' }}">
        </div>
    </div>
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-list" style="color: #16a34a;"></i> Services Links (4 items)</div>
    @for($i = 0; $i < 4; $i++)
    <div class="feature-card" style="margin-bottom: 1rem;">
        <div class="row-grid">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Title</label>
                <input type="text" name="sections[services_links][{{ $i }}][title]" class="form-control" value="{{ $sections['services_links'][$i]['title'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">URL</label>
                <input type="text" name="sections[services_links][{{ $i }}][url]" class="form-control" value="{{ $sections['services_links'][$i]['url'] ?? '' }}">
            </div>
        </div>
    </div>
    @endfor
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-link" style="color: #f59e0b;"></i> Company Links (4 items)</div>
    @for($i = 0; $i < 4; $i++)
    <div class="feature-card" style="margin-bottom: 1rem;">
        <div class="row-grid">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Title</label>
                <input type="text" name="sections[company_links][{{ $i }}][title]" class="form-control" value="{{ $sections['company_links'][$i]['title'] ?? '' }}">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">URL</label>
                <input type="text" name="sections[company_links][{{ $i }}][url]" class="form-control" value="{{ $sections['company_links'][$i]['url'] ?? '' }}">
            </div>
        </div>
    </div>
    @endfor
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-phone" style="color: #8b5cf6;"></i> Contact Info</div>
    <div class="row-grid" style="margin-bottom: 1rem;">
        @for($i = 0; $i < 2; $i++)
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Phone {{ $i + 1 }}</label>
            <input type="text" name="sections[contact_phones][{{ $i }}][number]" class="form-control" value="{{ $sections['contact_phones'][$i]['number'] ?? '' }}">
        </div>
        @endfor
    </div>
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Support Label</label>
        <input type="text" name="sections[contact_support_label]" class="form-control" value="{{ $sections['contact_support_label'] ?? '' }}">
    </div>
    <div class="row-grid" style="margin-bottom: 1rem;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Email</label>
            <input type="text" name="sections[contact_email]" class="form-control" value="{{ $sections['contact_email'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Email Label</label>
            <input type="text" name="sections[contact_email_label]" class="form-control" value="{{ $sections['contact_email_label'] ?? '' }}">
        </div>
    </div>
    <div class="row-grid">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Address</label>
            <input type="text" name="sections[contact_address]" class="form-control" value="{{ $sections['contact_address'] ?? '' }}">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">City</label>
            <input type="text" name="sections[contact_city]" class="form-control" value="{{ $sections['contact_city'] ?? '' }}">
        </div>
    </div>
</div>

<div class="crm-grid" style="margin-bottom: 2rem;">
    <div class="section-header"><i class="fas fa-copyright" style="color: #64748b;"></i> Copyright & WhatsApp</div>
    <div class="row-grid">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Copyright Text</label>
            <input type="text" name="sections[copyright_text]" class="form-control" value="{{ $sections['copyright_text'] ?? '' }}">
            <div class="help-text">Use {year} for auto year</div>
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">WhatsApp Number</label>
            <input type="text" name="sections[whatsapp_number]" class="form-control" value="{{ $sections['whatsapp_number'] ?? '' }}">
        </div>
    </div>
    <div style="margin-top: 1rem;">
        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">WhatsApp Default Message</label>
        <input type="text" name="sections[whatsapp_message]" class="form-control" value="{{ $sections['whatsapp_message'] ?? '' }}">
    </div>
</div>
