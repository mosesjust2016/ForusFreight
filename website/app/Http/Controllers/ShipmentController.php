<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
     *
     * Triggers ShipmentObserver::created() which:
     * - Sends email notification to customer
     * - Creates initial TrackingEvent
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'tracking_number' => 'required|string|unique:shipments,tracking_number',
            'serial_no' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'origin_country' => 'required|string|max:255',
            'origin_city' => 'required|string|max:255',
            'port_of_origin' => 'nullable|string|max:255',
            'destination_country' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'port_destination' => 'nullable|string|max:255',
            'service_type' => 'required|string',
            'shipping_method' => 'required|string',
            'initial_status' => 'required|string',
            'date_of_load' => 'required|date',
            'estimated_delivery' => 'required|date|after_or_equal:date_of_load',
            'description' => 'required|string',
            'no_of_parcels' => 'required|integer|min:1',
            'cbm_volume' => 'required|numeric|min:0',
            'gross_weight' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $origin = $validated['origin_country'] . ', ' . $validated['origin_city'];
        $destination = $validated['destination_country'] . ', ' . $validated['destination_city'];

        $user = Auth::user();
        $isGuest = !$user;
        
        if ($isGuest) {
            $user = User::findOrCreateByPhone(
                $validated['client_phone'],
                $validated['client_name']
            );

            if ($user->isTemporary() && !$user->hasVerifiedPhone()) {
                $phoneOtp = $user->generatePhoneOtp();
                app(\App\Services\SmsService::class)->sendOtp($user->phone, $phoneOtp);
            }
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shipment-images', 'public');
                $imagePaths[] = $path;
            }
        }

        $shipment = Shipment::create([
            'user_id' => $user->id,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'tracking_number' => $validated['tracking_number'],
            'serial_no' => $validated['serial_no'] ?? null,
            'code' => $validated['code'] ?? null,
            'origin' => $origin,
            'destination' => $destination,
            'port_of_origin' => $validated['port_of_origin'] ?? null,
            'port_destination' => $validated['port_destination'] ?? null,
            'service_type' => $validated['service_type'],
            'shipping_method' => $validated['shipping_method'],
            'status' => $validated['initial_status'],
            'date_of_load' => $validated['date_of_load'],
            'estimated_delivery' => $validated['estimated_delivery'],
            'description' => $validated['description'],
            'no_of_parcels' => $validated['no_of_parcels'],
            'cbm_volume' => $validated['cbm_volume'],
            'gross_weight' => $validated['gross_weight'],
            'cost' => $validated['cost'] ?? 0,
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        $message = 'Shipment created successfully! Tracking number: ' . $shipment->tracking_number;
        
        if ($isGuest && $user->isTemporary()) {
            $message .= '. Please activate your account by verifying your phone number.';
            return redirect()->route('activation.phone', ['phone' => $user->phone])
                ->with('success', $message);
        }

        return redirect()->route('client.shipments')
            ->with('success', $message);
    }

    /**
     * Show client warehouse cargo
     */
    public function warehouseCargo()
    {
        $query = \App\Models\WarehouseCargo::where('user_id', Auth::id());

        if (request('status') === 'in_warehouse') {
            $query->where('status', 'In Warehouse');
        } elseif (request('status') === 'shipped') {
            $query->where('status', '!=', 'In Warehouse');
        }

        $cargos = $query->latest()->paginate(20);

        $stats = [
            'total' => \App\Models\WarehouseCargo::where('user_id', Auth::id())->count(),
            'cartons' => \App\Models\WarehouseCargo::where('user_id', Auth::id())->sum('cartons'),
            'weight' => number_format(\App\Models\WarehouseCargo::where('user_id', Auth::id())->sum('gross_weight'), 2),
            'volume' => number_format(\App\Models\WarehouseCargo::where('user_id', Auth::id())->sum('volume'), 2),
        ];

        return view('client.warehouse-cargo', compact('cargos', 'stats'));
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
