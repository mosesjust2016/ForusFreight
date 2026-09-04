@extends('layouts.dashboard')

@section('title', 'Create CMS Page - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); }
    .form-control { padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.9rem; outline: none; background: white; width: 100%; }
    .form-control:focus { border-color: #22c55e; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }
    .btn-primary { background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.9rem; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark);">Create New Page</h1>
    <p style="color: var(--text-gray); font-size: 0.9rem;">Add a new page to your website.</p>
</div>

<div class="crm-grid">
    <form action="{{ route('admin.cms.pages.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Page Slug *</label>
                <input type="text" name="slug" class="form-control" placeholder="e.g. about-us" required>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">URL will be /{slug}</div>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Page Title *</label>
                <input type="text" name="title" class="form-control" placeholder="Page Title" required>
            </div>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Meta Description</label>
            <input type="text" name="meta_description" class="form-control" placeholder="SEO description...">
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Status</label>
            <select name="status" class="form-control" style="width: auto;">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Create Page</button>
            <a href="{{ route('admin.cms.pages.index') }}" style="padding: 0.75rem 1.5rem; background: #f1f5f9; color: #475569; border-radius: 10px; font-weight: 700; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
