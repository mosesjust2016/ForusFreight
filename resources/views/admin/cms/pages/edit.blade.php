@extends('layouts.dashboard')

@section('title', 'Edit: ' . $page->title . ' - CMS - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .section-editor { background: #f8fafc; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
    .section-header { font-weight: 800; font-size: 0.85rem; color: var(--text-gray); text-transform: uppercase; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-control { padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; width: 100%; }
    .form-control:focus { border-color: #22c55e; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }
    textarea.form-control { min-height: 100px; resize: vertical; }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
    .btn-upload { background: #e2e8f0; color: #475569; padding: 0.5rem 1rem; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-upload:hover { background: #cbd5e1; }
    .row-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .row-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .row-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .feature-card { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; }
    .tab-nav { display: flex; gap: 0.5rem; border-bottom: 2px solid #f1f5f9; margin-bottom: 1.5rem; }
    .tab-nav button { padding: 0.6rem 1rem; background: none; border: none; border-bottom: 2px solid transparent; font-weight: 800; color: #94a3b8; cursor: pointer; font-size: 0.85rem; }
    .tab-nav button.active { color: #22c55e; border-bottom-color: #22c55e; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .help-text { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; }
    .stat-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem; }
    .image-upload-wrapper { display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem; }
    .image-preview { width: 120px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #e2e8f0; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .image-preview img { width: 100%; height: 100%; object-fit: cover; }
    .image-preview .placeholder { color: #94a3b8; font-size: 0.7rem; text-align: center; }
    .upload-progress { display: none; font-size: 0.75rem; color: #22c55e; }
    .upload-progress.active { display: inline; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">{{ $page->title }}</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Slug: /{{ $page->slug }} &middot; Last edited: {{ $page->updated_at->diffForHumans() }}</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            @if($page->status === 'published')
            <a href="{{ url('/') }}@if($page->slug !== 'home')/{{ $page->slug }}@endif" target="_blank" class="btn-secondary"><i class="fas fa-eye"></i> Preview</a>
            @endif
            <a href="{{ route('admin.cms.pages.index') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<form action="{{ route('admin.cms.pages.update', $page) }}" method="POST" id="cmsForm">
    @csrf
    @method('PUT')

    <div class="tab-nav">
        <button type="button" class="active" onclick="switchTab('general')">General</button>
        <button type="button" onclick="switchTab('content')">Page Content</button>
        <button type="button" onclick="switchTab('seo')">SEO</button>
    </div>

    <!-- GENERAL TAB -->
    <div id="tab-general" class="tab-panel active">
        <div class="crm-grid" style="margin-bottom: 2rem;">
            <div class="row-grid">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Page Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Status</label>
                    <select name="status" class="form-control" style="width: auto;">
                        <option value="draft" {{ $page->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $page->status === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENT TAB -->
    <div id="tab-content" class="tab-panel">
        @if($page->slug === 'home')
            @include('admin.cms.pages.sections.home', ['sections' => $page->sections ?? []])
        @elseif($page->slug === 'about')
            @include('admin.cms.pages.sections.about', ['sections' => $page->sections ?? []])
        @elseif($page->slug === 'services')
            @include('admin.cms.pages.sections.services', ['sections' => $page->sections ?? []])
        @elseif($page->slug === 'contact')
            @include('admin.cms.pages.sections.contact', ['sections' => $page->sections ?? []])
        @elseif($page->slug === 'terms')
            @include('admin.cms.pages.sections.terms', ['sections' => $page->sections ?? []])
        @elseif($page->slug === 'footer')
            @include('admin.cms.pages.sections.footer', ['sections' => $page->sections ?? []])
        @else
            @include('admin.cms.pages.sections.generic', ['sections' => $page->sections ?? []])
        @endif
    </div>

    <!-- SEO TAB -->
    <div id="tab-seo" class="tab-panel">
        <div class="crm-grid">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="3" placeholder="SEO description for search engines...">{{ $page->meta_description }}</textarea>
                <div class="help-text">Recommended: 150-160 characters</div>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ $page->meta_keywords }}" placeholder="logistics, freight, zambia, south africa">
                <div class="help-text">Comma-separated keywords</div>
            </div>
        </div>
    </div>

    <div style="position: sticky; bottom: 2rem; background: white; padding: 1rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
        <button type="submit" class="btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;"><i class="fas fa-save"></i> Save Changes</button>
    </div>
</form>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    function switchTab(id) {
        document.querySelectorAll('.tab-nav button').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('tab-' + id).classList.add('active');
    }

    function handleImageUpload(input, previewId, hiddenId) {
        const file = input.files[0];
        if (!file) return;

        const preview = document.getElementById(previewId);
        const hidden = document.getElementById(hiddenId);
        const progress = input.parentElement.querySelector('.upload-progress');

        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        progress.classList.add('active');
        progress.textContent = 'Uploading...';

        fetch('{{ route('admin.cms.upload') }}', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.url) {
                hidden.value = data.url;
                preview.innerHTML = '<img src="' + data.url + '" alt="Preview">';
                progress.textContent = 'Uploaded!';
                setTimeout(() => { progress.classList.remove('active'); }, 2000);
            } else {
                progress.textContent = 'Upload failed';
                setTimeout(() => { progress.classList.remove('active'); }, 3000);
            }
        })
        .catch(() => {
            progress.textContent = 'Error';
            setTimeout(() => { progress.classList.remove('active'); }, 3000);
        });
    }

    tinymce.init({
        selector: '.rich-editor',
        height: 400,
        menubar: false,
        plugins: 'lists link image code table',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '{{ route('admin.cms.upload') }}',
        images_upload_handler: function (blobInfo) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '{{ route('admin.cms.upload') }}');

                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.onload = function () {
                    if (xhr.status != 200) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    var json = JSON.parse(xhr.responseText);
                    if (!json.url || typeof json.url != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    resolve(json.url);
                };

                var formData = new FormData();
                formData.append('image', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', '{{ csrf_token() }}');
                xhr.send(formData);
            });
        }
    });
</script>
@endsection
