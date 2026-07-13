<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\CommunicationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $shipments = ($user->is_admin || $user->hasPermission('admin.shipments.view'))
            ? Shipment::with('user')->latest()->get()
            : collect();

        return view('admin.dashboard', compact('shipments'));
    }

    public function shipments()
    {
        $shipments = Shipment::with('user')->latest()->paginate(20);

        return view('admin.shipments.index', compact('shipments'));
    }

    public function createShipment()
    {
        $clients  = User::where('is_admin', false)->get();
        $statuses = [
            'Order Placed', 'Pending', 'In Transit', 'At Border',
            'Cleared', 'Out for Delivery', 'Delivered', 'Cancelled',
        ];

        return view('admin.shipments.create', compact('clients', 'statuses'));
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
            'origin'             => 'required|string',
            'destination'        => 'required|string',
            'status'             => 'required|string',
            'estimated_delivery' => 'nullable|date',
            'cost'               => 'nullable|numeric',
            'weight'             => 'nullable|numeric',
            'dimensions'         => 'nullable|string',
            'description'        => 'nullable|string',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Generate unique serial number
        $serialNo = $this->generateSerialNo($validated['origin']);

        $shipment = Shipment::create([
            'user_id'            => $validated['user_id'],
            'serial_no'          => $serialNo,
            'origin'             => $validated['origin'],
            'destination'        => $validated['destination'],
            'status'             => $validated['status'],
            'estimated_delivery' => $validated['estimated_delivery'] ?? null,
            'cost'               => $validated['cost'] ?? 0,
            'weight'             => $validated['weight'] ?? null,
            'dimensions'         => $validated['dimensions'] ?? null,
            'description'        => $validated['description'] ?? null,
        ]);
        // Observer will automatically send email & create tracking event

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store("shipments/{$shipment->id}", 'public');
            }
            $shipment->update(['images' => $paths]);
        }

        return redirect()->route('admin.shipments')
            ->with('success', 'Shipment created successfully! Customer notification sent. Serial: ' . $shipment->serial_no);
    }

    /**
     * Generate unique serial number
     */
    private function generateSerialNo(string $origin): string
    {
        $prefix = strtoupper(substr($origin, 0, 3));
        $number = rand(10000, 99999);

        // Ensure uniqueness
        while (Shipment::where('serial_no', "{$prefix}.{$number}")->exists()) {
            $number = rand(10000, 99999);
        }

        return "{$prefix}.{$number}";
    }

    public function editShipment(Shipment $shipment)
    {
        $clients = User::where('is_admin', false)->get();
        $statuses = [
            'Order Placed',
            'Pending',
            'In Transit',
            'At Border',
            'Cleared',
            'Out for Delivery',
            'Delivered',
            'Cancelled',
        ];

        return view('admin.shipments.edit', compact('shipment', 'clients', 'statuses'));
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
            'origin'             => 'nullable|string',
            'destination'        => 'nullable|string',
            'current_border'     => 'nullable|string',
            'estimated_delivery' => 'nullable|date',
            'cost'               => 'nullable|numeric',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $existing = $shipment->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $existing[] = $file->store("shipments/{$shipment->id}", 'public');
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
        $activeShipments = Shipment::whereIn('status', ['In Transit', 'Out for Delivery'])->count();
        $deliveredShipments = Shipment::where('status', 'Delivered')->count();
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
}
