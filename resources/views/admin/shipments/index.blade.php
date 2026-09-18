@extends('layouts.dashboard')

@section('title', 'Global Shipment Management - Forus Freight')

@section('styles')
<style>
    .admin-shipment-grid {
        background: white;
        border-radius: 30px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .shipment-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0 1rem;
    }

    .shipment-table th {
        text-align: left;
        padding: 1rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-gray);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f8fafc;
    }

    .shipment-row {
        background: #fff;
        transition: all 0.3s;
    }

    .shipment-row:hover {
        background: #fcfdfe;
        transform: scale(1.002);
    }

    .shipment-row td {
        padding: 1.5rem 0.5rem;
        border-top: 1px solid #f8fafc;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .shipment-row td:first-child {
        border-left: 1px solid #f8fafc;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }

    .shipment-row td:last-child {
        border-right: 1px solid #f8fafc;
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .tracking-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        background: #f1f5f9;
        color: #475569;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .status-pill {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
    }

    .status-pending { background: #fffbeb; color: #b45309; }
    .status-transit { background: #eff6ff; color: #1e40af; }
    .status-delivered { background: #f0fdf4; color: #166534; }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: #64748b;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
        background: #f8fafc;
        text-decoration: none;
    }

    .action-btn:hover {
        background: var(--primary-green);
        color: white;
    }

</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Global Shipment Registry</h1>
            <p style="color: var(--text-gray); font-size: 0.9rem;">Comprehensive oversight of all cargo movements across the network.</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div style="background: white; padding: 0.75rem 1.5rem; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid #f1f5f9;">
                <span style="display: block; font-size: 0.65rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Active Freight</span>
                <span style="font-size: 1.25rem; font-weight: 900; color: var(--primary-green);">{{ $stats['active'] }}</span>
            </div>
            <div style="background: white; padding: 0.75rem 1.5rem; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid #f1f5f9;">
                <span style="display: block; font-size: 0.65rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">Total Load</span>
                <span style="font-size: 1.25rem; font-weight: 900; color: var(--text-dark);">{{ $stats['total'] }}</span>
            </div>
            <a href="{{ route('admin.shipments.create') }}" style="background: var(--primary-green); color: white; padding: 0.75rem 1.5rem; border-radius: 15px; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i> New Shipment
            </a>
            <button type="button" id="bulk-delete-btn" onclick="submitBulkDelete()" disabled
                style="background: #fef2f2; color: #dc2626; padding: 0.75rem 1.5rem; border-radius: 15px; font-size: 0.85rem; font-weight: 800; border: 1px solid #fecaca; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; opacity: 0.5;">
                <i class="fas fa-trash-can"></i> Delete Selected (<span id="bulk-count">0</span>)
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.shipments') }}" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
        <div style="position: relative; flex: 1; max-width: 420px;">
            <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-gray); font-size: 0.85rem;"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by tracking number or customer name..."
                style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border-radius: 15px; border: 1px solid #e2e8f0; font-size: 0.85rem; box-sizing: border-box;">
        </div>
        <button type="submit" style="background: var(--text-dark); color: white; padding: 0.75rem 1.5rem; border-radius: 15px; font-size: 0.85rem; font-weight: 800; border: none; cursor: pointer;">
            Search
        </button>
        @if($search !== '')
        <a href="{{ route('admin.shipments') }}" style="background: #f1f5f9; color: var(--text-gray); padding: 0.75rem 1.5rem; border-radius: 15px; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: flex; align-items: center;">
            Clear
        </a>
        @endif
    </form>
</div>

<div class="admin-shipment-grid">
    <div class="table-scroll">
    <table class="shipment-table">
        <thead>
            <tr>
                <th style="width: 35px;">
                    <input type="checkbox" id="select-all-shipments" title="Select all on this page">
                </th>
                <th>Tracking ID</th>
                <th>Client / Customer</th>
                <th>Origin → Destination</th>
                <th>Cargo Details</th>
                <th>Financials</th>
                <th>Current Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $shipment)
                <tr class="shipment-row">
                    <td>
                        <input type="checkbox" class="shipment-check" value="{{ $shipment->id }}" title="Select {{ $shipment->tracking_number ?: $shipment->serial_no }}">
                    </td>
                    <td>
                        <span class="tracking-badge">{{ $shipment->tracking_number ?: $shipment->serial_no }}</span>
                        <div style="font-size: 0.65rem; color: var(--text-gray); margin-top: 0.4rem; font-weight: 700;">
                            CREATED: {{ $shipment->created_at->format('d M, H:i') }}
                        </div>
                    </td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">{{ substr($shipment->user ? $shipment->user->name : 'G', 0, 1) }}</div>
                            <div style="max-width: 170px; overflow: hidden;">
                                <div title="{{ $shipment->user ? $shipment->user->name : 'Guest Client' }}" style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $shipment->user ? $shipment->user->name : 'Guest Client' }}</div>
                                <div title="{{ $shipment->user ? ($shipment->user->email ?: 'No email on file') : 'N/A' }}" style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $shipment->user ? ($shipment->user->email ?: 'No email on file') : 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem;">{{ $shipment->origin }}</div>
                        <div style="font-size: 0.75rem; color: var(--primary-green); font-weight: 700;">↓</div>
                        <div style="font-weight: 800; color: var(--text-dark); font-size: 0.9rem;">{{ $shipment->destination }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-dark);">{{ $shipment->description ?: ($shipment->service ?: 'General Cargo') }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-gray); font-weight: 600;">{{ $shipment->border_status ? 'Border: ' . $shipment->border_status : 'No border update' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 1rem; font-weight: 900; color: var(--text-dark);">
                            {{ usd($shipment->cost ?? 0) }}
                        </div>
                        <div style="font-size: 0.7rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase;">
                            ETA: {{ $shipment->estimated_delivery ? $shipment->estimated_delivery->format('M d') : 'TBD' }}
                        </div>
                    </td>
                    <td>
                        @php
                            $canonical = \App\Models\Shipment::canonicalStatus($shipment->status);
                            $statusClass = match($canonical) {
                                'DELIVERED' => 'status-delivered',
                                'EXCEPTION', 'ON_HOLD' => 'status-pending',
                                default => 'status-transit'
                            };
                            $icon = match($canonical) {
                                'DELIVERED' => 'fa-check-double',
                                'EXCEPTION', 'ON_HOLD' => 'fa-triangle-exclamation',
                                default => 'fa-truck-fast'
                            };
                        @endphp
                        <div class="status-pill {{ $statusClass }}">
                            <i class="fas {{ $icon }}"></i> {{ $shipment->status_label }}
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('admin.shipments.edit', $shipment) }}" class="action-btn" title="Edit Shipment"><i class="fas fa-pen-to-square"></i></a>
                            <a href="{{ route('admin.shipments.edit', $shipment) }}#add-event" class="action-btn" title="Add Tracking Event"><i class="fas fa-location-dot"></i></a>
                            <form method="POST" action="{{ route('admin.shipments.destroy', $shipment) }}" onsubmit="return confirm('Delete this shipment permanently? Tracking events will be removed too.')" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Delete Shipment" style="background:#fef2f2; color:#dc2626;"><i class="fas fa-trash-can"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 5rem 0; color: var(--text-gray);">
                        <i class="fas fa-box-open" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                        <h3 style="font-weight: 800;">No Shipments Found</h3>
                        <p style="font-size: 0.9rem;">
                            @if($search !== '')
                                No shipments match "{{ $search }}". Try a different tracking number or customer name.
                            @else
                                There are no active or historical shipments in the system.
                            @endif
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    @if($shipments->count() > 0)
    <div style="margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; text-align: center;">
        {{ $shipments->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>

<script>
(function () {
    var items = document.querySelectorAll('.shipment-check');
    var allBox   = document.getElementById('select-all-shipments');
    var bulkBtn  = document.getElementById('bulk-delete-btn');
    var countEl  = document.getElementById('bulk-count');

    function refresh() {
        var checked = document.querySelectorAll('.shipment-check:checked').length;
        countEl.textContent = checked;
        bulkBtn.disabled = checked === 0;
        bulkBtn.style.opacity = checked === 0 ? '0.5' : '1';
        bulkBtn.style.cursor = checked === 0 ? 'not-allowed' : 'pointer';
        if (allBox) {
            allBox.checked = items.length > 0 && checked === items.length;
        }
    }

    items.forEach(function (cb) { cb.addEventListener('change', refresh); });
    if (allBox) {
        allBox.addEventListener('change', function () {
            items.forEach(function (cb) { cb.checked = allBox.checked; });
            refresh();
        });
    }
})();

function submitBulkDelete() {
    var ids = Array.prototype.map.call(
        document.querySelectorAll('.shipment-check:checked'),
        function (cb) { return cb.value; }
    );
    if (!ids.length) return;
    if (!confirm('Delete ' + ids.length + ' selected shipment(s) permanently? Tracking events will be removed too.')) return;

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.shipments.bulk-delete') }}';

    var token = document.createElement('input');
    token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
    form.appendChild(token);

    var method = document.createElement('input');
    method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
    form.appendChild(method);

    ids.forEach(function (id) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
