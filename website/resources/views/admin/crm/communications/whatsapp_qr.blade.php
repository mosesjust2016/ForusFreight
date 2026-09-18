@extends('layouts.dashboard')

@section('title', 'WhatsApp QR Authorization - Forus Freight')

@section('styles')
<style>
    .crm-grid { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); text-align: center; max-width: 600px; margin: 0 auto; }
    .btn-whatsapp { background: #22c55e; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-whatsapp:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34, 197, 94, 0.3); }
    .btn-secondary { background: #f1f5f9; color: #475569; padding: 0.6rem 1.25rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
    .qr-frame { width: 100%; height: 500px; border: 2px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
    .qr-frame iframe { width: 100%; height: 100%; border: none; }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem; text-align: center;">
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">WhatsApp Authorization</h1>
    <p style="color: var(--text-gray); font-size: 0.9rem;">Scan the QR code with your WhatsApp Business app to authorize the account.</p>
</div>

<div class="crm-grid">
    @if($qrData['success'] && $qrData['qr'])
        <div style="margin-bottom: 1.5rem;">
            <div class="qr-frame">
                <iframe src="{{ $qrData['qr'] }}" title="WhatsApp QR Code" sandbox="allow-scripts allow-same-origin"></iframe>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <a href="{{ $qrData['qr'] }}" target="_blank" class="btn-secondary" style="margin-bottom: 0.75rem; display: inline-flex;">
                <i class="fas fa-external-link-alt"></i> Open QR in New Tab
            </a>
        </div>

        <p style="color: var(--text-gray); font-size: 0.9rem; margin-bottom: 1.5rem;">
            <i class="fas fa-info-circle" style="color: #3b82f6;"></i> 
            Open WhatsApp on your phone → Settings → Linked Devices → Link a Device → Scan the QR code above.
            <br><span style="font-size: 0.8rem; color: #94a3b8;">The QR auto-refreshes every 30 seconds.</span>
        </p>
    @else
        <div style="padding: 3rem 0;">
            <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
            <p style="font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem;">Unable to retrieve QR code</p>
            <p style="color: var(--text-gray); font-size: 0.9rem;">{{ $qrData['message'] ?? 'Please check your Green API configuration.' }}</p>
        </div>
    @endif

    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="{{ route('admin.crm.communications.whatsapp') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to WhatsApp</a>
        <a href="{{ route('admin.crm.communications.whatsapp.qr') }}" class="btn-whatsapp"><i class="fas fa-sync-alt"></i> Refresh QR</a>
    </div>
</div>
@endsection
