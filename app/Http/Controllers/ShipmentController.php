<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureImageUploadService;

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
        $parcelCode = $this->generateParcelCode();

        return view('client.create-shipment', compact('parcelCode'));
    }

    /**
     * Generate a unique client parcel code of the form "ZMFFL <6 digits>".
     * The client writes this code on their parcel so it is routed to Zambia
     * (ZMFFL) rather than Ghana (GHFFL).
     */
    private function generateParcelCode(): string
    {
        $prefix = config('forus.parcel_code_prefix', 'ZMFFL');

        do {
            $code = $prefix . ' ' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Shipment::where('code', $code)->exists());

        return $code;
    }

    /**
     * Generate a unique serial number of the form "<PREFIX>.#####" where the
     * prefix comes from the origin city (e.g. Durban -> DUR.37977). Mirrors
     * the AdminController behaviour so client-created shipments look identical.
     */
    private function generateSerialNo(string $originCity, string $originCountry): string
    {
        $source = $originCity ?: $originCountry;
        $prefix = strtoupper(substr(trim($source), 0, 3));

        do {
            $number = random_int(10000, 99999);
        } while (Shipment::where('serial_no', "{$prefix}.{$number}")->exists());

        return "{$prefix}.{$number}";
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
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
            'code' => 'nullable|string|max:50',
            'origin_country' => 'required|string|max:255',
            'origin_city' => 'required|string|max:255',
            'port_of_origin' => 'nullable|string|max:255',
            'destination_country' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'port_destination' => 'nullable|string|max:255',
            'service_type' => 'required|string',
            'shipping_method' => 'required|string',
            'description' => 'required|string|max:255',
            'no_of_parcels' => 'required|integer|min:1',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'tracking_number.unique' => 'That tracking number is already registered to another shipment. Check the number, or leave it blank and our team will assign one.',
        ]);

        $origin = $validated['origin_country'] . ', ' . $validated['origin_city'];
        $destination = $validated['destination_country'] . ', ' . $validated['destination_city'];

        // Serial number is auto-generated (the client no longer types one).
        $serialNo = $this->generateSerialNo($validated['origin_city'], $validated['origin_country']);

        // New client requests always start at "Shipment Created" — logistics
        // dates, weights, volumes and costs are confirmed by the ops team.
        $initialStatus = 'CREATED';

        // The parcel code is auto-generated on the create page and echoed back
        // via a hidden field. Guard against a collision anyway.
        $code = isset($validated['code']) ? strtoupper(trim($validated['code'])) : null;
        if ($code) {
            while (Shipment::where('code', $code)->exists()) {
                $code = $this->generateParcelCode();
            }
        }

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
                try {
                    $imagePaths[] = SecureImageUploadService::store($image, 'shipment-images');
                } catch (\RuntimeException $e) {
                    return back()->withErrors(['images' => $e->getMessage()])->withInput();
                }
            }
        }

        $shipment = Shipment::create([
            'user_id' => $user->id,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'serial_no' => $serialNo,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'code' => $code,
            'origin' => $origin,
            'destination' => $destination,
            'port_of_origin' => $validated['port_of_origin'] ?? null,
            'port_destination' => $validated['port_destination'] ?? null,
            'service_type' => $validated['service_type'],
            'shipping_method' => $validated['shipping_method'],
            'status' => $initialStatus,
            'description' => $validated['description'],
            'no_of_parcels' => $validated['no_of_parcels'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        $message = 'Shipment created successfully! Serial No: ' . $shipment->serial_no;
        
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
