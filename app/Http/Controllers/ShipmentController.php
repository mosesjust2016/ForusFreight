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
     * Countries a shipment can start from / be delivered to, and the cities
     * we know for each. One source of truth for the create form's dropdowns,
     * its JavaScript, and the prefill defaults below.
     */
    private const ORIGIN_COUNTRIES = ['China', 'Zambia', 'South Africa', 'Zimbabwe', 'Botswana', 'Namibia', 'DRC', 'Tanzania', 'Malawi', 'Mozambique'];

    private const DESTINATION_COUNTRIES = ['Zambia', 'South Africa', 'Zimbabwe', 'Botswana', 'Namibia', 'DRC', 'Tanzania', 'Malawi', 'Mozambique'];

    private const CITIES = [
        'China' => ['Foshan', 'Guangzhou', 'Shenzhen', 'Shanghai', 'Ningbo', 'Qingdao', 'Xiamen', 'Yiwu'],
        'Zambia' => ['Lusaka', 'Kitwe', 'Ndola', 'Livingstone', 'Chipata', 'Kabwe', 'Solwezi'],
        'South Africa' => ['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth', 'East London'],
        'Zimbabwe' => ['Harare', 'Bulawayo', 'Mutare', 'Gweru', 'Victoria Falls'],
        'Botswana' => ['Gaborone', 'Francistown', 'Maun', 'Lobatse'],
        'Namibia' => ['Windhoek', 'Walvis Bay', 'Swakopmund', 'Luderitz'],
        'DRC' => ['Lubumbashi', 'Kinshasa', 'Goma', 'Kolwezi'],
        'Tanzania' => ['Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma'],
        'Malawi' => ['Lilongwe', 'Blantyre', 'Mzuzu', 'Zomba'],
        'Mozambique' => ['Maputo', 'Beira', 'Nampula', 'Tete'],
    ];

    /**
     * List client shipments
     */
    public function index(Request $request)
    {
        // Simple "On the way" / "Delivered" filter for the client portal
        $show = $request->query('show') === 'delivered' ? 'delivered' : 'active';
        $delivered = Shipment::statusesForCanonical(['DELIVERED']);

        $base = Shipment::where('user_id', Auth::id());
        $counts = [
            'active' => (clone $base)->whereNotIn('status', $delivered)->count(),
            'delivered' => (clone $base)->whereIn('status', $delivered)->count(),
        ];

        $shipments = $base
            ->when($show === 'delivered',
                fn ($q) => $q->whereIn('status', $delivered),
                fn ($q) => $q->whereNotIn('status', $delivered))
            ->with('trackingEvents')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('client.shipments', compact('shipments', 'show', 'counts'));
    }

    /**
     * Show form to create new shipment
     *
     * The form is prefilled: customers get their own details and last-used
     * delivery route, plus sensible defaults for the usual China order.
     */
    public function create()
    {
        $parcelCode = $this->generateParcelCode();

        // Staff opening the client form are creating a shipment for someone
        // else, so only real customers get their details prefilled.
        $account = Auth::user();
        if ($account && $account->isStaff()) {
            $account = null;
        }

        return view('client.create-shipment', [
            'parcelCode' => $parcelCode,
            'account' => $account,
            'originCountries' => self::ORIGIN_COUNTRIES,
            'destinationCountries' => self::DESTINATION_COUNTRIES,
            'cities' => self::CITIES,
            'defaults' => $this->shipmentDefaults($account),
        ]);
    }

    /**
     * Defaults for the "bought in China, ship it home" request: goods are
     * collected at our China warehouse (Foshan) and travel by sea to the
     * customer's usual delivery city — their last shipment's, or Lusaka.
     */
    private function shipmentDefaults(?User $user): array
    {
        $defaults = [
            'origin_country' => 'China',
            'origin_city' => 'Foshan',
            'destination_country' => 'Zambia',
            'destination_city' => 'Lusaka',
            'shipping_method' => 'SEA',
            'no_of_parcels' => 1,
        ];

        if (! $user) {
            return $defaults;
        }

        $lastDestination = Shipment::where('user_id', $user->id)->latest('id')->value('destination');

        if (! is_string($lastDestination)) {
            return $defaults;
        }

        // Destination has been stored in three shapes over the years:
        // "Zambia, Lusaka" (what this form writes), "Lusaka, Zambia", and
        // just "Lusaka". Find the city and country wherever they are.
        $parts = array_values(array_filter(array_map('trim', explode(',', $lastDestination, 2)), function ($part) {
            return $part !== '' && strcasecmp($part, 'Other') !== 0;
        }));

        if (count($parts) === 1) {
            // City only — the country is whatever list it belongs to.
            foreach (self::DESTINATION_COUNTRIES as $country) {
                foreach (self::CITIES[$country] ?? [] as $known) {
                    if (strcasecmp($parts[0], $known) === 0) {
                        $defaults['destination_country'] = $country;
                        $defaults['destination_city'] = $known;
                        return $defaults;
                    }
                }
            }

            return $defaults;
        }

        if (count($parts) < 2) { // nothing usable was left after filtering
            return $defaults;
        }

        if (in_array($parts[0], self::DESTINATION_COUNTRIES, true)) {
            [$country, $city] = $parts;
        } elseif (in_array($parts[1], self::DESTINATION_COUNTRIES, true)) {
            [$city, $country] = $parts;
        } else {
            return $defaults;
        }

        $defaults['destination_country'] = $country;
        $defaults['destination_city'] = $city;

        return $defaults;
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
     *
     * For customers this is a section of the Account page now; staff still
     * get the standalone list they use from the admin sidebar.
     */
    public function invoices()
    {
        if (! Auth::user()->isStaff()) {
            return redirect()->to(route('client.profile') . '#invoices');
        }

        $invoices = Invoice::whereHas('shipment', function($query) {
            $query->where('user_id', Auth::id());
        })->latest()->paginate(10);

        return view('client.invoices.index', compact('invoices'));
    }
}
