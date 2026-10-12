<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\CommunicationsLog;
use App\Models\LandingPage;
use App\Models\LandingPageVisit;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Services\SecureImageUploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;

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
        $statusFilter = $request->query('status', '');
        $methodFilter = $request->query('shipping_method', '');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;

        $query = Shipment::with('user')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('serial_no', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($statusFilter !== '' && Shipment::canonicalStatus($statusFilter)) {
            $query->whereIn('status', Shipment::statusesForCanonical([Shipment::canonicalStatus($statusFilter)]));
        }
        if ($methodFilter !== '') {
            $query->where('shipping_method', $methodFilter);
        }

        $stats = [
            'active' => (clone $query)->whereNotIn('status', Shipment::statusesForCanonical(['DELIVERED', 'EXCEPTION']))->count(),
            'total' => (clone $query)->count(),
        ];

        $shipments = $query->paginate($perPage)->withQueryString();

        $statusOptions = Shipment::statusLabels();
        $shippingMethods = Shipment::query()->whereNotNull('shipping_method')->where('shipping_method', '!=', '')->distinct()->orderBy('shipping_method')->pluck('shipping_method');

        return view('admin.shipments.index', compact('shipments', 'search', 'stats', 'statusFilter', 'methodFilter', 'perPage', 'statusOptions', 'shippingMethods'));
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

        if ($validated['status'] !== 'CREATED') {
            return back()->withErrors(['status' => 'New shipments must start at Shipment Created.'])->withInput();
        }

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
     * Generate the printable Release Instruction PDF for a shipment.
     */
    public function deliveryNote(Shipment $shipment)
    {
        $shipment->load('user');

        $issuedAt = Carbon::now();
        $deliveryDate = $shipment->delivery_date ?? $issuedAt;
        $noteNumber = 'RI-'.$shipment->serial_no.'-'.$deliveryDate->format('Ymd');
        $verificationToken = $this->deliveryVerificationToken($shipment);
        $verificationUrl = route('delivery-notes.verify', ['token' => $verificationToken]);

        $logoPath = public_path('images/logo-transparent.png');
        $logoData = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        // QR Code with embedded logo
        $qrCode = QrCode::create($verificationUrl)->setSize(190)->setMargin(4);
        $qrLogo = is_file($logoPath) ? Logo::create($logoPath)->setResizeToWidth(40) : null;
        $writer = new PngWriter();
        $qr = $qrLogo ? $writer->write($qrCode, $qrLogo) : $writer->write($qrCode);

        // Visual artwork assets (embedded directly as base64 for reliable DomPDF / print rendering)
        $headerArtPath = public_path('images/delivery-note/header-logistics.png');
        $headerArtData = is_file($headerArtPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($headerArtPath)) : null;

        $parcelBoxPath = public_path('images/delivery-note/parcel-box-transparent.png');
        if (!is_file($parcelBoxPath)) {
            $parcelBoxPath = public_path('images/delivery-note/parcel-box.png');
        }
        $parcelBoxData = is_file($parcelBoxPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($parcelBoxPath)) : null;

        $signaturePath = public_path('images/delivery-note/signature-transparent.png');
        $signatureData = is_file($signaturePath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($signaturePath)) : null;

        $paymentMethodsSvgPath = public_path('images/delivery-note/payment-methods.svg');
        $paymentMethodsData = is_file($paymentMethodsSvgPath)
            ? 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($paymentMethodsSvgPath))
            : null;

        $trackingNumber = $shipment->tracking_number ?: ($shipment->serial_no ?: 'SHP482 1239 8476');
        $trackingNumberClean = preg_replace('/[^A-Za-z0-9]/', '', $trackingNumber);

        $barcodeTopSvg = $this->deliveryBarcodeSvg($trackingNumber, 34, 1.25, '#0d1b20');
        $barcodeBottomSvg = $this->deliveryBarcodeSvg($trackingNumberClean ?: $trackingNumber, 52, 2.1, '#0f172a');

        $senderName = config('forus.forwarding_address.name', 'Forus Freight Global Hub');
        $senderAddress = config('forus.forwarding_address.line1', 'Guangzhou / Foshan') . ', ' . config('forus.forwarding_address.country', 'China');

        $chargesFormatted = $shipment->cost && (float)$shipment->cost > 0
            ? '$' . number_format((float)$shipment->cost, 2)
            : '$5,000';

        $viewData = [
            'shipment'             => $shipment,
            'company'              => config('forus.company'),
            'issuedAt'             => $issuedAt,
            'deliveryDate'         => $deliveryDate,
            'noteNumber'           => $noteNumber,
            'verificationUrl'      => $verificationUrl,
            'verificationQr'       => $qr->getDataUri(),
            'logoData'             => $logoData,
            'headerArtData'        => $headerArtData,
            'parcelBoxData'        => $parcelBoxData,
            'signatureData'        => $signatureData,
            'paymentMethodsData'   => $paymentMethodsData,
            'trackingNumber'       => $trackingNumber,
            'trackingNumberClean'  => $trackingNumberClean,
            'barcodeTopSvg'        => 'data:image/svg+xml;base64,'.base64_encode($barcodeTopSvg),
            'barcodeBottomSvg'     => 'data:image/svg+xml;base64,'.base64_encode($barcodeBottomSvg),
            'senderName'           => $senderName,
            'senderAddress'        => $senderAddress,
            'chargesFormatted'     => $chargesFormatted,
        ];

        if (request()->query('preview') === 'html' || request()->query('view') === 'html') {
            return view('admin.shipments.delivery-note', $viewData);
        }

        $pdf = Pdf::loadView('admin.shipments.delivery-note', $viewData)
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);

        if (request()->query('download')) {
            return $pdf->download($noteNumber.'.pdf');
        }

        // Stream inline by default so users can view in browser and print
        return $pdf->stream($noteNumber.'.pdf');
    }

    public function verifyDeliveryNote(string $token)
    {
        $payload = base64_decode(strtr($token, '-_', '+/'));
        $payload = $payload !== false ? rtrim($payload, "\0") : false;
        [$shipmentId, $serialNo, $signature] = $payload !== false ? array_pad(explode('|', $payload, 3), 3, null) : [null, null, null];

        $expected = hash_hmac('sha256', ($shipmentId ?? '').'|'.($serialNo ?? ''), config('app.key'));
        $shipment = hash_equals($expected, (string) $signature)
            ? Shipment::with('user')->find($shipmentId)
            : null;

        abort_unless($shipment && hash_equals((string) $shipment->serial_no, (string) $serialNo), 404);

        return view('delivery-notes.verify', compact('shipment', 'token'));
    }

    private function deliveryVerificationToken(Shipment $shipment): string
    {
        $payload = $shipment->id.'|'.$shipment->serial_no;
        $signature = hash_hmac('sha256', $payload, config('app.key'));

        return rtrim(strtr(base64_encode($payload.'|'.$signature), '+/', '-_'), '=');
    }

    private function deliveryBarcodeSvg(string $value, int $height = 40, float $barWidth = 1.6, string $color = '#000000'): string
    {
        $table = [
            '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
            '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
            '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
            '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
            '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
            '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
            '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
            '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
            '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
            '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
            '114131', '311141', '411131', '211412', '211214', '211232', '2331112'
        ];

        $clean = preg_replace('/[^ -~]/', '', (string)$value);
        if ($clean === '') {
            $clean = '000000';
        }

        $codes = [104];
        $sum = 104;
        $len = strlen($clean);
        for ($i = 0; $i < $len; $i++) {
            $val = ord($clean[$i]) - 32;
            $codes[] = $val;
            $sum += $val * ($i + 1);
        }
        $codes[] = $sum % 103;
        $codes[] = 106;

        $pattern = '';
        foreach ($codes as $code) {
            $pattern .= $table[$code];
        }

        $totalUnits = 0;
        for ($i = 0; $i < strlen($pattern); $i++) {
            $totalUnits += (int)$pattern[$i];
        }

        $svgWidth = (int)ceil($totalUnits * $barWidth);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$svgWidth.'" height="'.$height.'" viewBox="0 0 '.$svgWidth.' '.$height.'">';
        $x = 0;
        for ($i = 0; $i < strlen($pattern); $i++) {
            $unitWidth = (float)$pattern[$i] * $barWidth;
            if ($i % 2 === 0) {
                $svg .= '<rect x="'.round($x, 2).'" y="0" width="'.round($unitWidth, 2).'" height="'.$height.'" fill="'.$color.'"/>';
            }
            $x += $unitWidth;
        }
        $svg .= '</svg>';

        return $svg;
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
            'status'      => 'nullable|in:' . implode(',', array_keys(Shipment::statusLabels())),
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'event_time'  => 'required|date|before_or_equal:now',
        ]);

        if (!empty($validated['status'])) {
            if ($error = $shipment->statusTransitionError($validated['status'])) {
                return back()->withErrors(['status' => $error])->withInput();
            }

            $latestStatusEvent = $shipment->trackingEvents()
                ->whereNotNull('status')
                ->latest('event_time')
                ->first();

            if ($latestStatusEvent && Carbon::parse($validated['event_time'])->lt($latestStatusEvent->event_time)) {
                return back()->withErrors(['event_time' => 'A new status update cannot be dated before the previous status update.'])->withInput();
            }
        }

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
        if (!empty($validated['status']) && Shipment::canonicalStatus($validated['status']) !== Shipment::canonicalStatus($shipment->status)) {
            $shipment->update(['status' => $validated['status']]);
        }

        return redirect()->route('admin.shipments.edit', $shipment)
            ->withFragment('add-event')
            ->with('success', 'Tracking event added successfully.');
    }

    /**
     * Delete a tracking event (previously a no-op `fn () => back()` route
     * that the edit page happily submitted to).
     */
    public function destroyTrackingEvent(TrackingEvent $event)
    {
        $shipment = $event->shipment;

        $event->delete();

        return redirect()->route('admin.shipments.edit', $shipment)
            ->with('success', 'Tracking event removed.');
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
            'next_action'        => 'nullable|string|max:255',
            'estimated_delivery' => 'nullable|date',
            'date_of_load'       => 'nullable|date',
            'delivery_date'      => 'nullable|date',
            'proof_of_delivery'  => 'nullable|string|max:255',
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
        $statusChanged = Shipment::canonicalStatus($validated['status']) !== Shipment::canonicalStatus($shipment->status);
        $oldStatus = $shipment->status;

        if ($error = $shipment->statusTransitionError($validated['status'])) {
            return back()->withErrors(['status' => $error])->withInput();
        }

        // Marking delivered without a date used to leave delivery_date null,
        // which made daysInTransit keep counting — stamp it automatically.
        if (Shipment::canonicalStatus($validated['status'] ?? null) === 'DELIVERED' && empty($validated['delivery_date'])) {
            $validated['delivery_date'] = now()->toDateString();
        }

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

    public function reports(Request $request)
    {
        // Keep this database agnostic: SQLite does not provide MySQL's YEAR()
        // function, and the report must work in local and production setups.
        $availableYears = Shipment::query()->pluck('created_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->unique()
            ->sortDesc()
            ->values();
        // Keep 2025 available even when there are no shipments recorded in
        // that year yet, so staff can open an empty historical report.
        $firstYear = min(2025, $availableYears->last() ?: Carbon::now()->year);
        $availableYears = collect(range($firstYear, Carbon::now()->year))->sortDesc()->values();
        $selectedYear = (int) $request->query('year', Carbon::now()->year);
        if (!$availableYears->contains($selectedYear)) {
            $selectedYear = Carbon::now()->year;
        }
        $yearStart = Carbon::create($selectedYear, 1, 1)->startOfYear();
        $yearEnd = $yearStart->copy()->endOfYear();
        $yearQuery = Shipment::whereBetween('created_at', [$yearStart, $yearEnd]);

        $totalShipments = (clone $yearQuery)->count();
        $activeShipments = (clone $yearQuery)->whereIn('status', Shipment::statusesForCanonical(['DEPARTED_CN', 'IN_TRANSIT', 'ARRIVED_ZM']))->count();
        $deliveredShipments = (clone $yearQuery)->whereIn('status', Shipment::statusesForCanonical(['DELIVERED']))->count();
        $totalRevenue = (clone $yearQuery)->sum('cost');
        $totalClients = User::where('is_admin', false)->count();

        $monthlyRevenue = (clone $yearQuery)->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', $selectedYear)
            ->sum('cost');

        $leadsCount = User::where('is_admin', false)->where('crm_status', 'lead')->count();

        $shipmentStats = (clone $yearQuery)->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $communicationQuery = CommunicationsLog::whereBetween('created_at', [$yearStart, $yearEnd]);
        $commStats = (clone $communicationQuery)->selectRaw("channel, count(*) as count")
            ->groupBy('channel')->pluck('count', 'channel');
        $communicationMetrics = [
            'sent' => (clone $communicationQuery)->outgoing()->count(),
            'received' => (clone $communicationQuery)->incoming()->count(),
            'delivered' => (clone $communicationQuery)->outgoing()->whereIn('status', ['sent', 'delivered'])->count(),
            'failed' => (clone $communicationQuery)->where('status', 'failed')->count(),
            'response_rate' => 0,
        ];
        $communicationMetrics['response_rate'] = $communicationMetrics['sent'] > 0
            ? round(($communicationMetrics['received'] / $communicationMetrics['sent']) * 100, 1)
            : 0;
        $recentLogs = CommunicationsLog::with('user')->whereBetween('created_at', [$yearStart, $yearEnd])->latest()->take(10)->get();

        $landingPageQuery = LandingPage::whereBetween('created_at', [$yearStart, $yearEnd]);
        $landingPageMetrics = [
            'pages' => (clone $landingPageQuery)->count(),
            'views' => (clone $landingPageQuery)->sum('views'),
            'submissions' => (clone $landingPageQuery)->sum('submissions'),
            'conversion_rate' => 0,
        ];
        $landingPageMetrics['conversion_rate'] = $landingPageMetrics['views'] > 0
            ? round(($landingPageMetrics['submissions'] / $landingPageMetrics['views']) * 100, 1)
            : 0;
        $landingVisits = LandingPageVisit::whereBetween('created_at', [$yearStart, $yearEnd]);
        $visitCount = (clone $landingVisits)->count();
        $landingPageMetrics['bounce_rate'] = $visitCount > 0 ? round(((clone $landingVisits)->where('bounced', true)->count() / $visitCount) * 100, 1) : 0;
        $landingPageMetrics['average_time'] = $visitCount > 0 ? round((clone $landingVisits)->avg('duration_seconds')) : 0;
        $landingPageMetrics['tracked_visits'] = $visitCount;
        $landingPageMetrics['cta_clicks'] = (clone $landingVisits)->where('cta_clicked', true)->count();
        $landingPageMetrics['cta_rate'] = $visitCount > 0 ? round(($landingPageMetrics['cta_clicks'] / $visitCount) * 100, 1) : 0;
        $trafficSources = (clone $landingVisits)->selectRaw("COALESCE(NULLIF(source, ''), 'direct') as source, count(*) as visits")
            ->groupBy('source')->orderByDesc('visits')->get();
        $landingPageReport = (clone $landingPageQuery)->orderByDesc('views')->get()->map(function ($page) {
            $page->report_conversion_rate = $page->views > 0 ? round(($page->submissions / $page->views) * 100, 1) : 0;
            return $page;
        });

        $allShipments = (clone $yearQuery)->with('user')->get();
        $statusLabels = Shipment::statusLabels();
        $statusBreakdown = $allShipments->groupBy(fn ($shipment) => Shipment::canonicalStatus($shipment->status) ?: 'UNKNOWN')
            ->map(fn ($group) => $group->count())
            ->sortDesc();
        $statusBreakdown = $statusBreakdown->mapWithKeys(fn ($count, $code) => [$statusLabels[$code] ?? $code => $count]);

        $monthlyShipments = collect(range(11, 0))->mapWithKeys(function ($monthsAgo) use ($selectedYear) {
            $date = Carbon::create($selectedYear, 1, 1)->startOfYear()->addMonths(11 - $monthsAgo);
            return [$date->format('M Y') => Shipment::whereBetween('created_at', [$date, $date->copy()->endOfMonth()])->count()];
        });

        $monthlyRevenueTrend = collect(range(11, 0))->mapWithKeys(function ($monthsAgo) use ($selectedYear) {
            $date = Carbon::create($selectedYear, 1, 1)->startOfYear()->addMonths(11 - $monthsAgo);
            return [$date->format('M Y') => (float) Shipment::whereBetween('created_at', [$date, $date->copy()->endOfMonth()])->sum('cost')];
        });

        $routeBreakdown = $allShipments->groupBy(fn ($shipment) => ($shipment->origin ?: 'Unknown') . ' → ' . ($shipment->destination ?: 'Unknown'))
            ->map(fn ($group) => ['shipments' => $group->count(), 'revenue' => (float) $group->sum('cost')])
            ->sortByDesc('shipments')->take(10);
        $methodBreakdown = $allShipments->groupBy(function ($shipment) {
                $method = strtoupper(trim((string) $shipment->shipping_method));
                return ['AIR' => 'Air', 'SEA' => 'Sea', 'ROAD' => 'Road', 'RAIL' => 'Rail'][$method] ?? ($method ?: 'Not recorded');
            })
            ->map(fn ($group) => ['shipments' => $group->count(), 'revenue' => (float) $group->sum('cost')])
            ->sortByDesc('shipments');
        foreach (['AIR' => 'Air', 'SEA' => 'Sea', 'ROAD' => 'Road', 'RAIL' => 'Rail'] as $code => $label) {
            if (!$methodBreakdown->has($label)) {
                $methodBreakdown->put($label, ['shipments' => 0, 'revenue' => 0]);
            }
        }
        $topClients = $allShipments->groupBy(fn ($shipment) => $shipment->client_name ?: ($shipment->user->name ?? 'Unknown client'))
            ->map(fn ($group) => ['shipments' => $group->count(), 'revenue' => (float) $group->sum('cost')])
            ->sortByDesc('shipments')->take(10);

        $delivered = $allShipments->filter(fn ($shipment) => Shipment::canonicalStatus($shipment->status) === 'DELIVERED');
        $onTimeDelivered = $delivered->filter(fn ($shipment) => $shipment->delivery_date && $shipment->estimated_delivery && $shipment->delivery_date->lte($shipment->estimated_delivery))->count();
        $lateDelivered = $delivered->filter(fn ($shipment) => $shipment->delivery_date && $shipment->estimated_delivery && $shipment->delivery_date->gt($shipment->estimated_delivery))->count();
        $deliveryMetrics = [
            'delivered' => $delivered->count(),
            'on_time' => $onTimeDelivered,
            'late' => $lateDelivered,
            'on_time_rate' => ($onTimeDelivered + $lateDelivered) > 0 ? round(($onTimeDelivered / ($onTimeDelivered + $lateDelivered)) * 100, 1) : null,
            'average_days' => $delivered->filter(fn ($s) => $s->date_of_load && $s->delivery_date)->avg(fn ($s) => $s->date_of_load->diffInDays($s->delivery_date)),
        ];

        $dataQuality = [
            'missing_tracking' => $allShipments->filter(fn ($s) => blank($s->tracking_number))->count(),
            'missing_eta' => $allShipments->filter(fn ($s) => blank($s->estimated_delivery))->count(),
            'missing_weight' => $allShipments->filter(fn ($s) => is_null($s->gross_weight) && is_null($s->weight))->count(),
            'missing_location' => $allShipments->filter(fn ($s) => blank($s->current_border))->count(),
        ];

        $exceptionShipments = $allShipments->filter(fn ($s) => in_array(Shipment::canonicalStatus($s->status), ['ON_HOLD', 'EXCEPTION'], true))->sortByDesc('updated_at')->take(10);

        return view('admin.reports', compact(
            'totalShipments',
            'activeShipments',
            'deliveredShipments',
            'totalRevenue',
            'totalClients',
            'monthlyRevenue',
            'leadsCount',
            'shipmentStats',
            'commStats', 'communicationMetrics',
            'recentLogs', 'landingPageMetrics', 'landingPageReport', 'trafficSources', 'statusBreakdown', 'monthlyShipments', 'monthlyRevenueTrend', 'routeBreakdown', 'methodBreakdown', 'topClients', 'deliveryMetrics', 'dataQuality', 'exceptionShipments', 'selectedYear', 'availableYears'
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
