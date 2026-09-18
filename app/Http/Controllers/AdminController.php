<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\CommunicationLog;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Services\SecureImageUploadService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $canViewShipments = $user->is_admin || $user->hasPermission('admin.shipments.view');

        $shipmentStats = ['total' => 0, 'inTransit' => 0, 'pending' => 0];
        $shipments = collect();
        $recentUpdates = collect();

        if ($canViewShipments) {
            // Stats need to reflect ALL shipments, not just the current page,
            // so they're counted separately from the paginated registry list
            // below (which previously loaded every shipment unpaginated —
            // an ever-growing page as the shipment count grew).
            $shipmentStats = [
                'total'     => Shipment::count(),
                'inTransit' => Shipment::whereIn('status', Shipment::statusesForCanonical(['DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM']))->count(),
                'pending'   => Shipment::whereIn('status', Shipment::statusesForCanonical(['CREATED', 'AWAITING_RECEIPT', 'RECEIVED_CN']))->count(),
            ];

            $shipments = Shipment::with('user')->latest()->paginate(10);
            $recentUpdates = Shipment::with('user')->latest()->take(5)->get();
        }

        return view('admin.dashboard', compact('shipments', 'shipmentStats', 'recentUpdates'));
    }

    public function shipments(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = Shipment::with('user')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('serial_no', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $stats = [
            'active' => (clone $query)->whereNotIn('status', Shipment::statusesForCanonical(['DELIVERED', 'EXCEPTION']))->count(),
            'total' => (clone $query)->count(),
        ];

        $shipments = $query->paginate(10)->withQueryString();

        return view('admin.shipments.index', compact('shipments', 'search', 'stats'));
    }

    public function createShipment()
    {
        $clients   = User::where('is_admin', false)->get();
        $statuses  = \App\Models\Shipment::statusLabels();
        $nextSerial = Shipment::nextSerialNumber();

        return view('admin.shipments.create', compact('clients', 'statuses', 'nextSerial'));
    }

    /**
     * Store new shipment (Admin)
     *
     * Triggers ShipmentObserver::created() which:
     * - Sends email notification to customer
     * - Creates initial TrackingEvent
     */
    public function storeShipment(Request $request)
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'serial_no'          => 'nullable|string|max:255',
            'origin'             => 'required|string',
            'destination'        => 'required|string',
            'status'             => 'required|string',
            'estimated_delivery' => 'nullable|date',
            'date_of_load'       => 'nullable|date',
            'cost'               => 'nullable|numeric',
            'weight'             => 'nullable|numeric',
            'cbm_volume'         => 'nullable|numeric|min:0',
            'gross_weight'       => 'nullable|numeric|min:0',
            'no_of_parcels'      => 'nullable|integer|min:1',
            'dimensions'         => 'nullable|string',
            'description'        => 'nullable|string',
            'reference'          => 'nullable|string|max:255',
            'phone_number'       => 'nullable|string|max:60',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Serial is auto-generated in the form "ZMFFL-000001" (incremental)
        // unless the admin supplies their own unique one.
        $serialNo = $validated['serial_no'] ?? $this->generateSerialNo();

        // Guarantee the serial has never been used before. Covers the small
        // window between the form page loading its next value and this form
        // being submitted (e.g. two admins creating shipments at the same
        // time), where the value shown could already be taken.
        while (Shipment::where('serial_no', $serialNo)->exists()) {
            $serialNo = $this->generateSerialNo();
        }

        $shipment = Shipment::create([
            'user_id'            => $validated['user_id'],
            'serial_no'          => $serialNo,
            'origin'             => $validated['origin'],
            'destination'        => $validated['destination'],
            'status'             => $validated['status'],
            'estimated_delivery' => $validated['estimated_delivery'] ?? null,
            'date_of_load'       => $validated['date_of_load'] ?? null,
            'cost'               => $validated['cost'] ?? 0,
            'weight'             => $validated['weight'] ?? null,
            'cbm_volume'         => $validated['cbm_volume'] ?? null,
            'gross_weight'       => $validated['gross_weight'] ?? null,
            'no_of_parcels'      => $validated['no_of_parcels'] ?? null,
            'dimensions'         => $validated['dimensions'] ?? null,
            'description'        => $validated['description'] ?? null,
            'reference'          => $validated['reference'] ?? null,
            'phone_number'       => $validated['phone_number'] ?? null,
        ]);
        // Observer will automatically send email & create tracking event

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                try {
                    $paths[] = SecureImageUploadService::store($file, "shipments/{$shipment->id}");
                } catch (\RuntimeException $e) {
                    // The shipment itself is already created and valid — don't
                    // lose it over a bad image file, just report it and move on.
                    return redirect()->route('admin.shipments.edit', $shipment)
                        ->with('error', 'Shipment created (Serial: ' . $shipment->serial_no . '), but one image was rejected: ' . $e->getMessage());
                }
            }
            $shipment->update(['images' => $paths]);
        }

        return redirect()->route('admin.shipments')
            ->with('success', 'Shipment created successfully! Customer notification sent. Serial: ' . $shipment->serial_no);
    }

    /**
     * Generate the next auto-increment serial number in the form
     * "ZMFFL-000001". Left in the controller so storeShipment() can
     * call it, while the actual sequence logic lives on the model.
     */
    private function generateSerialNo(): string
    {
        return Shipment::nextSerialNumber();
    }

    public function editShipment(Shipment $shipment)
    {
        $clients = User::where('is_admin', false)->get();
        $statuses = \App\Models\Shipment::statusLabels();

        return view('admin.shipments.edit', compact('shipment', 'clients', 'statuses'));
    }

    /**
     * Manually add a tracking event from the shipment edit page.
     *
     * This route previously pointed at editShipment() (a GET-only display
     * method that ignores the request body), so "Add Event" silently
     * re-rendered the edit page without creating anything.
     */
    public function storeTrackingEvent(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'location'    => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status'      => 'nullable|string',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'event_time'  => 'required|date',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'location'    => $validated['location'],
            'description' => $validated['description'],
            'status'      => $validated['status'] ?: null,
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'event_time'  => $validated['event_time'],
        ]);

        // Reuses the same status-change side effects (email/SMS notification)
        // as updateShipment() below via ShipmentObserver::updated().
        if (!empty($validated['status']) && $validated['status'] !== $shipment->status) {
            $shipment->update(['status' => $validated['status']]);
        }

        return redirect()->route('admin.shipments.edit', $shipment)
            ->withFragment('add-event')
            ->with('success', 'Tracking event added successfully.');
    }

    /**
     * Update shipment (Admin)
     *
     * Triggers ShipmentObserver::updated() which:
     * - Detects status changes
     * - Sends email notification to customer if status changed
     * - Creates TrackingEvent for status change
     * - Sends SMS update (if enabled)
     */
    public function updateShipment(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status'             => 'required|string',
            'user_id'            => 'required|exists:users,id',
            'serial_no'          => 'nullable|string|max:255|unique:shipments,serial_no,' . $shipment->id,
            'origin'             => 'nullable|string',
            'destination'        => 'nullable|string',
            'current_border'     => 'nullable|string',
            'estimated_delivery' => 'nullable|date',
            'date_of_load'       => 'nullable|date',
            'cost'               => 'nullable|numeric',
            'cbm_volume'         => 'nullable|numeric|min:0',
            'gross_weight'       => 'nullable|numeric|min:0',
            'no_of_parcels'      => 'nullable|integer|min:1',
            'reference'          => 'nullable|string|max:255',
            'phone_number'       => 'nullable|string|max:60',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Blank serial in the edit form means "keep the current one".
        if (empty($validated['serial_no'])) {
            $validated['serial_no'] = $shipment->serial_no;
        }

        $existing = $shipment->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $existing[] = SecureImageUploadService::store($file, "shipments/{$shipment->id}");
                } catch (\RuntimeException $e) {
                    return back()->withErrors(['images' => $e->getMessage()])->withInput();
                }
            }
        }

        // Check if status is changing for user feedback
        $statusChanged = $validated['status'] !== $shipment->status;
        $oldStatus = $shipment->status;

        $shipment->update(array_merge(
            \Illuminate\Support\Arr::except($validated, ['images']),
            ['images' => $existing]
        ));
        // Observer will automatically send email & create tracking event if status changed

        $message = 'Shipment updated successfully!';
        if ($statusChanged) {
            $message .= " Customer notification sent ({$oldStatus} → {$validated['status']})";
        }

        return redirect()->back()->with('success', $message);
    }

    public function removeShipmentImage(Request $request, Shipment $shipment)
    {
        $index  = $request->validate(['index' => 'required|integer|min:0'])['index'];
        $images = $shipment->images ?? [];

        if (!array_key_exists($index, $images)) {
            return back()->with('error', 'Image not found.');
        }

        $safePath = $images[$index];
        // Guard: only delete files that live under this shipment's own directory.
        abort_unless(str_starts_with($safePath, "shipments/{$shipment->id}/"), 403);
        Storage::disk('public')->delete($safePath);
        array_splice($images, $index, 1);
        $shipment->update(['images' => array_values($images)]);

        return back()->with('success', 'Image removed.');
    }

    public function clients(Request $request)
    {
        $query = User::whereDoesntHave('roles')->where('is_admin', false)->withCount('shipments');

        // Apply CRM status filter from sidebar
        if ($request->has('status') && in_array($request->status, ['lead', 'active', 'high_value', 'blocked'])) {
            $query->where('crm_status', $request->status);
        }

        $clients = $query->latest()->paginate(20);

        $base = User::whereDoesntHave('roles')->where('is_admin', false);
        $statusCounts = [
            'all'        => (clone $base)->count(),
            'lead'       => (clone $base)->where('crm_status', 'lead')->count(),
            'active'     => (clone $base)->where('crm_status', 'active')->count(),
            'high_value' => (clone $base)->where('crm_status', 'high_value')->count(),
            'blocked'    => (clone $base)->where('crm_status', 'blocked')->count(),
        ];

        $currentStatus = $request->get('status', 'all');

        return view('admin.clients.index', compact('clients', 'statusCounts', 'currentStatus'));
    }

    public function createClient()
    {
        return view('admin.clients.create');
    }

    public function showClient(User $user)
    {
        $shipments = $user->shipments()->latest()->get();
        $communicationLogs = $user->communicationLogs()->latest()->get();

        return view('admin.clients.show', compact('user', 'shipments', 'communicationLogs'));
    }

    public function editClient(User $user)
    {
        return view('admin.clients.edit', compact('user'));
    }

    public function reports()
    {

        $totalShipments = Shipment::count();
        $activeShipments = Shipment::whereIn('status', Shipment::statusesForCanonical(['DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM']))->count();
        $deliveredShipments = Shipment::whereIn('status', Shipment::statusesForCanonical(['DELIVERED']))->count();
        $totalRevenue = Shipment::sum('cost');
        $totalClients = User::where('is_admin', false)->count();

        $monthlyRevenue = Shipment::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('cost');

        $leadsCount = User::where('is_admin', false)->where('crm_status', 'lead')->count();

        $shipmentStats = Shipment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $commStats = CommunicationLog::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $recentLogs = CommunicationLog::with('user')->latest()->take(10)->get();

        return view('admin.reports', compact(
            'totalShipments',
            'activeShipments',
            'deliveredShipments',
            'totalRevenue',
            'totalClients',
            'monthlyRevenue',
            'leadsCount',
            'shipmentStats',
            'commStats',
            'recentLogs'
        ));
    }

    /**
     * Delete a single shipment. Tracking events are removed with it so no
     * orphaned rows are left behind.
     */
    public function deleteShipment(Shipment $shipment)
    {
        $label = $shipment->tracking_number ?: $shipment->serial_no;

        $shipment->trackingEvents()->delete();
        $shipment->delete();

        return back()->with('success', "Shipment deleted successfully ({$label}).");
    }

    /**
     * Bulk delete. Accepts an array of shipment IDs (from the checkbox
     * selection on the shipment registry). Deletes them along with their
     * tracking events in a single pass.
     */
    public function bulkDeleteShipments(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $ids = array_filter($validated['ids']);

        if (empty($ids)) {
            return back()->with('error', 'No shipments selected.');
        }

        $count = Shipment::whereIn('id', $ids)->count();
        TrackingEvent::whereIn('shipment_id', $ids)->delete();
        Shipment::whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} shipment(s) deleted successfully.");
    }
}
