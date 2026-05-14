@extends('layouts.dashboard')

@section('title', 'Portal Settings - Forus Freight')

@section('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .settings-section {
        background: white;
        border-radius: 30px;
        padding: 3.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .section-header i {
        width: 50px;
        height: 50px;
        background: var(--primary-green-light);
        color: var(--primary-green);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .section-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .setting-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 0;
        border-bottom: 1px solid #f8fafc;
    }

    .setting-item:last-child {
        border-bottom: none;
    }

    .setting-info h4 {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }

    .setting-info p {
        font-size: 0.85rem;
        color: var(--text-gray);
        font-weight: 600;
    }

    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e2e8f0;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--primary-green);
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    .select-standard {
        padding: 0.6rem 1rem;
        border-radius: 10px;
        border: 2px solid #f1f5f9;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-dark);
        background: #fcfdfe;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Portal Preferences</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Customize how you interact with the Forus Freight dashboard and notifications.</p>
    </div>
</div>

<div class="settings-grid">
    <!-- Notifications -->
    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-bell"></i>
            <h3>Notification Settings</h3>
        </div>
        
        <div class="setting-item">
            <div class="setting-info">
                <h4>Email Alerts</h4>
                <p>Receive shipment status updates via email.</p>
            </div>
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h4>SMS Notifications</h4>
                <p>Get instant text alerts when cargo passes borders.</p>
            </div>
            <label class="switch">
                <input type="checkbox">
                <span class="slider"></span>
            </label>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h4>Monthly Statements</h4>
                <p>Automatically receive billing summaries on the 1st.</p>
            </div>
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <!-- Preferences -->
    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-sliders"></i>
            <h3>Display Preferences</h3>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h4>Primary Currency</h4>
                <p>Choose the default currency for your invoices.</p>
            </div>
            <select class="select-standard">
                <option value="ZMW">ZMW - Zambian Kwacha</option>
                <option value="USD">USD - US Dollar</option>
                <option value="ZAR">ZAR - SA Rand</option>
            </select>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h4>Dashboard Layout</h4>
                <p>Toggle between detailed or compact views.</p>
            </div>
            <select class="select-standard">
                <option value="Spacious">Spacious (Default)</option>
                <option value="Compact">Compact</option>
            </select>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h4>Timezone</h4>
                <p>Set your local time for tracking timestamps.</p>
            </div>
            <select class="select-standard">
                <option value="CAT">Lusaka (GMT+2)</option>
                <option value="GMT">London (GMT+0)</option>
                <option value="SAST">Johannesburg (GMT+2)</option>
            </select>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 5rem;">
        <button style="background: var(--primary-green); color: white; padding: 1.25rem 3rem; border: none; border-radius: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(76,175,80,0.2);">
            Save All Preferences
        </button>
    </div>
</div>
@endsection
