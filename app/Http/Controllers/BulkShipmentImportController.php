<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\ShipmentNotifierService;

class BulkShipmentImportController extends Controller
{
    /**
     * Recognised header names for each shipment field, keyed by the
     * canonical field name. Import maps columns by header text (not
     * position), so column order in the file never matters and a
     * missing/renamed column is reported explicitly instead of silently
     * shifting every other field — this is what caused all 38 existing
     * shipments to have their origin value saved as the serial number.
     */
    private const SHIPMENT_HEADER_ALIASES = [
        'tracking_number' => ['tracking number', 'trackingno', 'tracking'],
        'code' => ['cargo code', 'code'],
        'serial_no' => ['serial number', 'serial no', 'serial'],
        'client_name' => ['client name', 'client'],
        'reference' => ['reference', 'ref'],
        'phone_number' => ['phone number'],
        'origin' => ['origin', 'origin country'],
        'port_of_origin' => ['origin port', 'port of origin'],
        'current_border' => ['current location', 'current border'],
        'destination' => ['destination', 'destination country'],
        'shipping_method' => ['shipping method'],
        'status' => ['status'],
        'date_of_load' => ['date loaded', 'date of load'],
        'estimated_delivery' => ['eta', 'estimated delivery'],
        'driver' => ['driver'],
        'vehicle_registration' => ['vehicle registration', 'vehicle reg'],
        'delivery_date' => ['delivery date'],
        'proof_of_delivery' => ['proof of delivery', 'pod'],
        'description' => ['cargo description', 'description'],
        'no_of_parcels' => ['parcel quantity', 'quantity', 'no of parcels'],
        'gross_weight' => ['weight (kg)', 'weight'],
        'cost' => ['cost (zmw)', 'cost'],
        'cbm_volume' => ['cbm', 'cbm volume', 'volume'],
        'client_phone' => ['client phone', 'phone'],
        'service' => ['cargo / service type', 'cargo service type', 'service type', 'service'],
    ];

    private const REQUIRED_SHIPMENT_FIELDS = ['client_name', 'origin', 'destination'];

    private const EVENT_HEADER_ALIASES = [
        'tracking_number' => ['tracking number', 'trackingno', 'tracking'],
        'event_time' => ['date/time', 'date time', 'datetime', 'date'],
        'status' => ['status'],
        'location' => ['location'],
        'description' => ['remarks', 'description'],
    ];

    private const REQUIRED_EVENT_FIELDS = ['tracking_number', 'status', 'location'];

    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('admin.bulk-shipment-import');
    }

    public function import(Request $request)
    {
        @set_time_limit(900);
        @ini_set('memory_limit', '512M');

        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel|max:5120',
        ], [
            'file.mimetypes' => 'The file field must be a file of type: csv, xlsx, xls.'
        ]);

        try {
            $rows = $this->parseFile($request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'File is empty or invalid format.');
        }

        $header = array_shift($rows);
        $map = $this->buildHeaderMap($header, self::SHIPMENT_HEADER_ALIASES);

        $missing = array_diff(self::REQUIRED_SHIPMENT_FIELDS, array_keys($map));
        if (!empty($missing)) {
            return back()->with('error', 'The file is missing required column(s): ' . implode(', ', array_map(
                fn ($field) => self::SHIPMENT_HEADER_ALIASES[$field][0],
                $missing
            )) . '. Found headers: ' . implode(', ', $header));
        }

        $results = ['success' => 0, 'updated' => 0, 'failed' => 0, 'errors' => [], 'shipments' => [], 'notifications' => []];

        foreach ($rows as $rowNum => $row) {
            $rowNum += 2; // header row + 1-indexed

            if (empty(array_filter($row))) continue;

            $result = $this->upsertShipmentFromRow($row, $map, $rowNum);

            if ($result['success']) {
                // Notify the owner (WhatsApp first, SMS fallback). Locally the
                // notifier redirects everything to the configured test phone.
                $notifier = app(ShipmentNotifierService::class);
                $notify   = $notifier->notifyOwner($result['model'], !$result['updated']);
                $result['shipment']['notify'] = $notify;
                $results['notifications'][$notify['status']] = ($results['notifications'][$notify['status']] ?? 0) + 1;

                $results[$result['updated'] ? 'updated' : 'success']++;
                $results['shipments'][] = $result['shipment'];
            } else {
                $results['failed']++;
                $results['errors'][] = $result['error'];
            }
        }

        $notifySummary = '';
        if (!empty($results['notifications'])) {
            $parts = [];
            if (!empty($results['notifications']['sent']))   $parts[] = $results['notifications']['sent'] . ' notified';
            if (!empty($results['notifications']['failed'])) $parts[] = $results['notifications']['failed'] . ' failed';
            if (!empty($results['notifications']['skipped']))$parts[] = $results['notifications']['skipped'] . ' no-phone/skipped';
            $notifySummary = ' · Notifications: ' . implode(', ', $parts);
        }

        return back()->with('import_results', $results)
            ->with('success', "Bulk import completed. {$results['success']} created, {$results['updated']} updated, {$results['failed']} failed.{$notifySummary}");
    }

    public function importTrackingEvents(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel|max:5120',
        ], [
            'file.mimetypes' => 'The file field must be a file of type: csv, xlsx, xls.'
        ]);

        try {
            $rows = $this->parseFile($request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'File is empty or invalid format.');
        }

        $header = array_shift($rows);
        $map = $this->buildHeaderMap($header, self::EVENT_HEADER_ALIASES);

        $missing = array_diff(self::REQUIRED_EVENT_FIELDS, array_keys($map));
        if (!empty($missing)) {
            return back()->with('error', 'The file is missing required column(s): ' . implode(', ', array_map(
                fn ($field) => self::EVENT_HEADER_ALIASES[$field][0],
                $missing
            )) . '. Found headers: ' . implode(', ', $header));
        }

        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        $sequenceByShipment = [];

        foreach ($rows as $rowNum => $row) {
            $rowNum += 2;

            if (empty(array_filter($row))) continue;

            $trackingNumber = trim($row[$map['tracking_number']] ?? '');
            $status = trim($row[$map['status']] ?? '');
            $location = trim($row[$map['location']] ?? '');
            $remarks = isset($map['description']) ? trim($row[$map['description']] ?? '') : '';
            $dateRaw = isset($map['event_time']) ? trim($row[$map['event_time']] ?? '') : '';

            if (!$trackingNumber) {
                $results['failed']++;
                $results['errors'][] = "Row $rowNum: Tracking Number is required";
                continue;
            }

            $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
            if (!$shipment) {
                $results['failed']++;
                $results['errors'][] = "Row $rowNum: No shipment found with Tracking Number '{$trackingNumber}'";
                continue;
            }

            $sequenceByShipment[$shipment->id] = ($sequenceByShipment[$shipment->id] ?? 0) + 1;

            $eventTime = $dateRaw ? $this->parseDate($dateRaw) : null;
            if (!$eventTime) {
                // No explicit date given ("—" in the source data): fall back to
                // the shipment's load date plus a small offset per event so
                // events still sort correctly by event_time as well as by
                // the explicit `sequence` column.
                $eventTime = ($shipment->date_of_load ?? now())->copy()
                    ->addMinutes($sequenceByShipment[$shipment->id]);
            }

            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $status ?: null,
                'location' => $location ?: 'Unknown',
                'description' => $remarks ?: ($status ?: 'Update'),
                'event_time' => $eventTime,
                'sequence' => $sequenceByShipment[$shipment->id],
            ]);

            $results['success']++;
        }

        return back()->with('import_events_results', $results)
            ->with('success', "Tracking events import completed. {$results['success']} added, {$results['failed']} failed.");
    }

    /**
     * Build a map of canonical field name => column index, based on the
     * file's actual header row rather than a fixed position.
     */
    private function buildHeaderMap(array $header, array $aliases): array
    {
        $normalized = array_map(fn ($h) => $this->normalizeHeader($h), $header);
        $map = [];

        foreach ($aliases as $field => $names) {
            foreach ($names as $name) {
                $index = array_search($this->normalizeHeader($name), $normalized, true);
                if ($index !== false) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        return preg_replace('/[^a-z0-9]+/', ' ', $header);
    }

    private function parseFile($file)
    {
        $path = $file->getRealPath();
        $filename = $file->getClientOriginalName();

        return str_ends_with($filename, '.csv') ? $this->parseCSV($path) : $this->parseExcel($path);
    }

    private function parseCSV($path)
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            if (!$header) $header = fgetcsv($handle, 0, "\t");
            $delimiter = (count($header) > 1) ? ',' : "\t";
            rewind($handle);
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($data) === 1 && empty($data[0])) continue;
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function parseExcel($path)
    {
        $rows = [];

        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
                $zip->close();

                foreach ($xml->sheetData->row as $row) {
                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $rowData[] = (string) $cell->v;
                    }
                    $rows[] = $rowData;
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return $rows;
    }

    private function upsertShipmentFromRow(array $row, array $map, int $rowNum): array
    {
        try {
            $get = fn (string $field, $default = '') => isset($map[$field]) ? trim($row[$map[$field]] ?? $default) : $default;

            $trackingNumber = $this->cleanNumericString($get('tracking_number'));
            $clientName = $get('client_name');
            $origin = $get('origin');
            $destination = $get('destination');

            if (!$clientName) return ['success' => false, 'error' => "Row $rowNum: Client name is required"];
            if (!$origin) return ['success' => false, 'error' => "Row $rowNum: Origin is required"];
            if (!$destination) return ['success' => false, 'error' => "Row $rowNum: Destination is required"];

            // Tracking Number is optional, just like in "Create New Shipment".
            // Supplying one matches/updates an existing shipment; leaving it
            // blank auto-generates a unique number for a brand-new shipment.
            $shipment = $trackingNumber ? Shipment::where('tracking_number', $trackingNumber)->first() : null;
            if (!$trackingNumber) {
                $trackingNumber = $this->generateTrackingNumber();
            }

            $serialNumber = $this->cleanNumericString($get('serial_no'));
            $weight = (float) preg_replace('/[^0-9.]/', '', $get('gross_weight', '0'));
            $quantity = (int) $get('no_of_parcels', '0');
            $cost = (float) preg_replace('/[^0-9.]/', '', $get('cost', '0'));
            $estimatedDelivery = $this->parseDate($get('estimated_delivery'));
            $deliveryDate = $this->parseDate($get('delivery_date'));
            $dateOfLoad = $this->parseDate($get('date_of_load'));

            // Auto-generate a serial number only for brand-new shipments that
            // didn't supply one; never overwrite an existing serial number.
            if (!$serialNumber) {
                $serialNumber = $shipment?->serial_no ?? Shipment::nextSerialNumber();
            }
            if (!$shipment) {
                $baseSerial = $serialNumber;
                $counter = 0;
                while (Shipment::where('serial_no', $serialNumber)->exists()) {
                    $counter++;
                    $serialNumber = $baseSerial . '-' . $counter;
                }
            }

            $attributes = [
                'client_name' => $clientName,
                'serial_no' => $serialNumber,
                'code' => $get('code') ?: null,
                'reference' => $get('reference') ?: null,
                'phone_number' => $get('phone_number') ?: null,
                'client_phone' => $get('client_phone') ?: null,
                'service' => $get('service') ?: null,
                'origin' => $origin,
                'port_of_origin' => $get('port_of_origin') ?: null,
                'current_border' => $get('current_border') ?: null,
                'destination' => $destination,
                'shipping_method' => $get('shipping_method') ?: null,
                'status' => $get('status') ?: 'CREATED',
                'date_of_load' => $dateOfLoad,
                'estimated_delivery' => $estimatedDelivery,
                'delivery_date' => $deliveryDate,
                'proof_of_delivery' => $get('proof_of_delivery') ?: null,
                'driver' => $get('driver') ?: null,
                'vehicle_registration' => $get('vehicle_registration') ?: null,
                'description' => $get('description') ?: null,
                'no_of_parcels' => $quantity > 0 ? $quantity : null,
                'quantity' => $quantity > 0 ? $quantity : null,
                'gross_weight' => $weight > 0 ? $weight : null,
                'weight' => $weight > 0 ? $weight : null,
                'cbm_volume' => $this->cleanNumericString($get('cbm_volume')) ?: null,
                'cost' => $cost,
            ];

            // Silent: bulk import supplies its own tracking history via
            // tracking_events.csv, so we don't want ShipmentObserver firing
            // a customer email + duplicate "shipment created" event per row.
            if ($shipment) {
                $shipment->update($attributes);
                $updated = true;
            } else {
                $shipment = Shipment::withoutEvents(function () use ($attributes, $trackingNumber) {
                    return Shipment::create(array_merge($attributes, [
                        'user_id' => $this->findOrCreateClient($attributes['client_name'])->id,
                        'tracking_number' => $trackingNumber,
                    ]));
                });
                $updated = false;
            }

            return [
                'success' => true,
                'updated' => $updated,
                'model'   => $shipment,
                'shipment' => [
                    'tracking_number' => $trackingNumber,
                    'serial' => $serialNumber,
                    'client' => $clientName,
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight > 0 ? $weight . ' KG' : 'N/A',
                ],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Row $rowNum: {$e->getMessage()}"];
        }
    }

    /**
     * Generate a unique Tracking Number of the form "RS.########" when a
     * row leaves the column blank (mirrors the existing tracking-number
     * format in the data, e.g. RS.26052049).
     */
    private function generateTrackingNumber(): string
    {
        do {
            $code = 'RS.' . random_int(10000000, 99999999);
        } while (Shipment::where('tracking_number', $code)->exists());

        return $code;
    }

    private function findOrCreateClient(string $clientName): User
    {
        $user = User::where('name', $clientName)->first();
        if ($user) {
            return $user;
        }

        return User::create([
            'name' => $clientName,
            'email' => null,
            'password' => 'temp' . uniqid(),
            'is_admin' => false,
        ]);
    }

    /**
     * Undo Excel's habit of rendering long numeric-looking codes in
     * scientific notation (e.g. "7.03004E+11") when a CSV is exported
     * from a spreadsheet. Values that aren't affected pass through
     * unchanged.
     */
    private function cleanNumericString(string $value): string
    {
        if (preg_match('/^(\d+)(?:\.\d+)?[Ee]\+?(\d+)$/', $value, $m)) {
            return bcmul($m[1], bcpow('10', $m[2]));
        }

        return $value;
    }

    private function parseDate(?string $date): ?\Carbon\Carbon
    {
        if (!$date) return null;
        $date = trim($date);

        // Date-only formats must be pinned to midnight — Carbon otherwise
        // fills the missing time-of-day with the current wall-clock time,
        // which made a same-day event with an explicit date sort *after*
        // later events whose date was synthesized (and correctly
        // defaulted to midnight).
        $dateOnlyFormats = ['d/m/Y', 'd-m-Y', 'Y-m-d'];
        foreach ($dateOnlyFormats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date)->startOfDay();
            } catch (\Exception $e) {
                continue;
            }
        }

        $dateTimeFormats = ['d/m/Y H:i', 'd/m/Y H:i:s', 'Y-m-d H:i:s'];
        foreach ($dateTimeFormats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function downloadTemplate()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $headers = [
            // Required columns first, in the same order as the "Required
            // Columns" list, then the optional columns following the
            // "Create New Shipment" form order (serial, reference, phone,
            // origin-quantity fields), then bulk-only extras.
            'Tracking Number', 'Client Name', 'Origin', 'Destination',
            'Serial Number', 'Reference', 'Phone Number',
            'Cargo / Service Type', 'Status', 'ETA', 'Date Loaded',
            'CBM (m³)', 'Weight (KG)', 'Parcel Quantity', 'Cost (ZMW)',
            'Cargo Code', 'Origin Port', 'Current Location', 'Shipping Method',
            'Driver', 'Vehicle Registration', 'Delivery Date', 'Proof of Delivery',
            'Cargo Description',
        ];

        return $this->csvResponse('shipments_import_template.csv', $headers, [
            ['773421428627451', 'Womba Kadimba', 'China', 'Lusaka, Zambia',
                'ZMFFL-000001', 'REF-2026-001', '+260 97 123 4567',
                'General Cargo', 'Ordered', '24/08/2026', '25/06/2026',
                '0.6', '500', '5', '15500.00',
                'ZMFFL 6982', 'Guangzhou Port', 'Port of Beira', 'Sea',
                '', '', '', '',
                'Toilet Cleaner'],
        ]);
    }

    public function downloadTrackingEventsTemplate()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $headers = ['Tracking Number', 'Date/Time', 'Status', 'Location', 'Remarks'];

        return $this->csvResponse('tracking_events_import_template.csv', $headers, [
            ['773421428627451', '25/06/2026', 'Ordered', 'China', 'Order created'],
            ['773421428627451', '', 'Loaded', 'Guangzhou', 'Cargo loaded'],
            ['773421428627451', '', 'In Transit', 'At Sea', 'Shipment departed'],
            ['773421428627451', '', 'Arrived at Port', 'Beira, Mozambique', 'Awaiting clearance'],
            ['773421428627451', '', 'In Transit', 'Lusaka', 'On final journey'],
            ['773421428627451', '', 'Delivered', 'Lusaka', 'Delivered to client'],
        ]);
    }

    private function csvResponse(string $filename, array $headers, array $rows)
    {
        $handle = fopen('php://memory', 'w');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
