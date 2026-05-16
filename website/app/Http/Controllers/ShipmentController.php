<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    /**
     * List client shipments
     */
    public function index()
    {
        $shipments = Shipment::where('user_id', Auth::id())
            ->with('trackingEvents')
            ->latest()
            ->paginate(10);
        
        return view('client.shipments', compact('shipments'));
    }

    /**
     * Show form to create new shipment
     */
    public function create()
    {
        return view('client.create-shipment');
    }

    /**
     * Store new shipment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'dimensions' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|string',
        ]);

        $shipment = Shipment::create([
            'user_id' => Auth::id(),
            'tracking_number' => 'FORUS-' . strtoupper(substr($validated['origin'], 0, 3)) . '-' . rand(1000, 9999),
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'status' => 'Order Placed',
            'weight' => $validated['weight'],
            'dimensions' => $validated['dimensions'] ?? null,
            'description' => $validated['description'] ?? null,
            'service_type' => $validated['service_type'],
        ]);

        return redirect()->route('client.shipments')->with('success', 'Shipment created successfully! Tracking number: ' . $shipment->tracking_number);
    }

    /**
     * Show client invoices
     */
    public function invoices()
    {
        $invoices = Invoice::whereHas('shipment', function($query) {
            $query->where('user_id', Auth::id());
        })->latest()->paginate(10);
        
        return view('client.invoices.index', compact('invoices'));
    }
}
