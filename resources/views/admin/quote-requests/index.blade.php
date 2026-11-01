@extends('layouts.dashboard')

@section('title', 'Quote Requests - Forus Freight')

@section('content')
<div class="welcome-section" style="margin-bottom:2rem;">
    <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-dark);">Quote Requests</h1>
    <p style="color:var(--text-gray);">Track enquiries from first contact through to a completed quote.</p>
</div>

<div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1.25rem;">
    @foreach([null => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'closed' => 'Closed'] as $key => $label)
        <a href="{{ route('admin.quote-requests', $key ? ['status' => $key] : []) }}" style="padding:.55rem 1rem;border-radius:10px;text-decoration:none;font-weight:800;font-size:.82rem;background:{{ $status === $key ? 'var(--primary-green)' : '#fff' }};color:{{ $status === $key ? '#fff' : '#64748b' }};border:1px solid #e2e8f0;">{{ $label }}</a>
    @endforeach
</div>

<div style="background:#fff;border:1px solid #e8eef0;border-radius:20px;overflow:auto;box-shadow:var(--shadow);">
<table style="width:100%;border-collapse:collapse;min-width:950px;">
    <thead><tr style="text-align:left;border-bottom:1px solid #edf2f3;color:#64748b;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">
        <th style="padding:1rem;">Requester</th><th style="padding:1rem;">Route</th><th style="padding:1rem;">Service</th><th style="padding:1rem;">Received</th><th style="padding:1rem;">Status</th><th style="padding:1rem;">Follow-up</th>
    </tr></thead>
    <tbody>
    @forelse($requests as $quote)
        <tr style="border-bottom:1px solid #f1f5f5;vertical-align:top;">
            <td style="padding:1rem;"><strong>{{ $quote->full_name }}</strong><div style="font-size:.78rem;color:#64748b;">{{ $quote->email }}<br>{{ $quote->phone }}</div></td>
            <td style="padding:1rem;font-size:.85rem;">{{ $quote->pickup }}<br><span style="color:#94a3b8;">↓</span><br>{{ $quote->delivery }}</td>
            <td style="padding:1rem;font-size:.85rem;">{{ ucwords(str_replace('-', ' ', $quote->service_type)) }}<br>{{ $quote->weight ? number_format($quote->weight, 2).' kg' : 'Weight not supplied' }}</td>
            <td style="padding:1rem;font-size:.82rem;color:#64748b;">{{ $quote->created_at->format('d M Y H:i') }}</td>
            <td style="padding:1rem;"><span style="display:inline-block;padding:.3rem .55rem;border-radius:999px;background:#eef7f5;color:#087a70;font-size:.7rem;font-weight:900;text-transform:uppercase;">{{ $quote->status }}</span></td>
            <td style="padding:1rem;min-width:260px;">
                <form action="{{ route('admin.quote-requests.update', $quote) }}" method="POST">
                    @csrf @method('PUT')
                    <div style="display:flex;gap:.4rem;"><select name="status" style="flex:1;padding:.5rem;border:1px solid #dce7e7;border-radius:8px;"><option value="new" @selected($quote->status==='new')>New</option><option value="contacted" @selected($quote->status==='contacted')>Contacted</option><option value="quoted" @selected($quote->status==='quoted')>Quoted</option><option value="closed" @selected($quote->status==='closed')>Closed</option></select><button type="submit" style="border:0;border-radius:8px;background:#0d3435;color:#fff;padding:0 .75rem;font-weight:800;cursor:pointer;">Save</button></div>
                    <textarea name="admin_notes" placeholder="Follow-up notes" style="width:100%;margin-top:.45rem;padding:.55rem;border:1px solid #dce7e7;border-radius:8px;font-size:.78rem;">{{ $quote->admin_notes }}</textarea>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" style="padding:3rem;text-align:center;color:#64748b;">No quote requests found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div style="margin-top:1rem;">{{ $requests->links() }}</div>
@endsection
