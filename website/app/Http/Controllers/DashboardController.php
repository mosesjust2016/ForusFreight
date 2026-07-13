<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the client dashboard.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // Stats
        $stats = [
            'active_shipments' => Shipment::where('user_id', $userId)
                ->whereNotIn('status', ['Delivered', 'Cancelled'])
                ->count(),
            'total_spent' => Invoice::where('user_id', $userId)
                ->where('status', 'Paid')
                ->sum('amount'),
            'pending_invoices' => Invoice::where('user_id', $userId)
                ->where('status', 'Pending')
                ->count(),
            'total_shipments' => Shipment::where('user_id', $userId)->count(),
        ];

        // Recent Shipments
        $recentShipments = Shipment::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Pending Payments
        $pendingPayments = Invoice::where('user_id', $userId)
            ->where('status', 'Pending')
            ->with('shipment')
            ->latest()
            ->get();

        // Real-time tracking lookup
        $trackedShipment = null;
        $trackingError = null;
        if ($request->has('serial_no')) {
            $serialNo = $request->query('serial_no');
            $trackedShipment = Shipment::where('serial_no', $serialNo)
                ->with('trackingEvents')
                ->first();
            if (!$trackedShipment) {
                $trackingError = 'Serial number not found. Please check and try again.';
            }
        }

        return view('dashboard', compact('stats', 'recentShipments', 'pendingPayments', 'trackedShipment', 'trackingError'));
    }
}
