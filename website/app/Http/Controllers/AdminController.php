<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\CommunicationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $shipments = Shipment::with('user')->latest()->get();
        
        return view('admin.dashboard', compact('shipments'));
    }

    /**
     * List all shipments
     */
    public function shipments()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $shipments = Shipment::with('user')->latest()->paginate(20);
        
        return view('admin.shipments.index', compact('shipments'));
    }

    /**
     * Show form to create new shipment
     */
    public function createShipment()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $clients = User::where('is_admin', false)->get();
        
        return view('admin.shipments.create', compact('clients'));
    }

    /**
     * Store new shipment
     */
    public function storeShipment(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'status' => 'required|string',
            'estimated_delivery' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $shipment = Shipment::create([
            'user_id' => $validated['user_id'],
            'tracking_number' => 'FORUS-' . strtoupper(substr($validated['origin'], 0, 3)) . '-' . rand(1000, 9999),
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'status' => $validated['status'],
            'estimated_delivery' => $validated['estimated_delivery'] ?? null,
            'cost' => $validated['cost'] ?? 0,
            'weight' => $validated['weight'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.shipments')->with('success', 'Shipment created successfully!');
    }

    /**
     * Show form to edit shipment
     */
    public function editShipment(Shipment $shipment)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

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
     * Update shipment
     */
    public function updateShipment(Request $request, Shipment $shipment)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'status' => 'required|string',
            'estimated_delivery' => 'nullable|date',
            'cost' => 'nullable|numeric',
        ]);

        $shipment->update($validated);

        return redirect()->route('admin.shipments')->with('success', 'Shipment updated successfully!');
    }

    /**
     * List all clients
     */
    public function clients(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = User::where('is_admin', false)->withCount('shipments');

        // Apply CRM status filter from sidebar
        if ($request->has('status') && in_array($request->status, ['lead', 'active', 'high_value', 'blocked'])) {
            $query->where('crm_status', $request->status);
        }

        $clients = $query->latest()->paginate(20);

        // Counts for filter badges
        $statusCounts = [
            'all'        => User::where('is_admin', false)->count(),
            'lead'       => User::where('is_admin', false)->where('crm_status', 'lead')->count(),
            'active'     => User::where('is_admin', false)->where('crm_status', 'active')->count(),
            'high_value' => User::where('is_admin', false)->where('crm_status', 'high_value')->count(),
            'blocked'    => User::where('is_admin', false)->where('crm_status', 'blocked')->count(),
        ];

        $currentStatus = $request->get('status', 'all');

        return view('admin.clients.index', compact('clients', 'statusCounts', 'currentStatus'));
    }

    /**
     * Show form to create new client
     */
    public function createClient()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        return view('admin.clients.create');
    }

    /**
     * Show client details
     */
    public function showClient(User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $shipments = $user->shipments()->latest()->get();
        
        return view('admin.clients.show', compact('user', 'shipments'));
    }

    /**
     * Show form to edit client
     */
    public function editClient(User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        return view('admin.clients.edit', compact('user'));
    }

    /**
     * Show reports
     */
    public function reports()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

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
