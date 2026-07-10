<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Company;

class BulkShipmentImportController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
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
        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel|max:5120',
        ], [
            'file.mimetypes' => 'The file field must be a file of type: csv, xlsx, xls.'
        ]);

        $file = $request->file('file');
        $results = ['success' => 0, 'failed' => 0, 'errors' => [], 'shipments' => []];

        try {
            $rows = $this->parseFile($file);
            
            if (empty($rows)) {
                return back()->with('error', 'File is empty or invalid format.');
            }

            foreach ($rows as $rowNum => $row) {
                $rowNum++; // Account for header row
                
                if (empty(array_filter($row))) continue; // Skip empty rows

                $result = $this->createShipmentFromRow($row, $rowNum);
                
                if ($result['success']) {
                    $results['success']++;
                    $results['shipments'][] = $result['shipment'];
                } else {
                    $results['failed']++;
                    $results['errors'][] = $result['error'];
                }
            }

            return back()->with('import_results', $results)
                        ->with('success', "Bulk import completed. {$results['success']} shipments created, {$results['failed']} failed.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function parseFile($file)
    {
        $path = $file->getRealPath();
        $filename = $file->getClientOriginalName();
        
        if (str_ends_with($filename, '.csv')) {
            return $this->parseCSV($path);
        } else {
            return $this->parseExcel($path);
        }
    }

    private function parseCSV($path)
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            if (!$header) $header = fgetcsv($handle, 0, "\t");
            $delimiter = (count($header) > 1) ? ',' : "\t";
            rewind($handle);
            fgetcsv($handle, 0, $delimiter);
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
                
                foreach ($xml->sheetData->row as $rowIdx => $row) {
                    if ($rowIdx === 0) continue; // Skip header
                    
                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $rowData[] = (string)$cell->v;
                    }
                    $rows[] = $rowData;
                }
            }
        } catch (\Exception $e) {
            // Fallback: return empty array
            return [];
        }
        
        return $rows;
    }

    private function createShipmentFromRow($row, $rowNum)
    {
        try {
            // Map columns: 0=Client, 1=Tracking, 2=Serial, 3=Origin, 4=Destination, 5=Type, 6=Weight, 7=Qty, 8=Status, 9=Cost, 10=Est.Delivery, 11=Driver, 12=VehicleReg, 13=Images
            $clientName = trim($row[0] ?? '');
            $trackingNumber = isset($row[1]) ? trim($row[1]) : '';
            $serialNumber = isset($row[2]) ? trim($row[2]) : '';
            $origin = isset($row[3]) ? trim($row[3]) : '';
            $destination = isset($row[4]) ? trim($row[4]) : '';
            $cargoType = isset($row[5]) ? trim($row[5]) : '';
            $weight = isset($row[6]) ? (float)preg_replace('/[^0-9.]/', '', $row[6] ?? '0') : 0;
            $quantity = isset($row[7]) ? (int)($row[7] ?? 1) : 1;
            $status = isset($row[8]) ? trim($row[8]) : '';
            $cost = isset($row[9]) ? (float)preg_replace('/[^0-9.]/', '', $row[9] ?? '0') : 0;
            $estimatedDelivery = isset($row[10]) ? trim($row[10]) : '';
            $driver = isset($row[11]) ? trim($row[11]) : '';
            $vehicleReg = isset($row[12]) ? trim($row[12]) : '';
            $imageNames = isset($row[13]) ? trim($row[13]) : '';

            // Validate required fields
            if (!$clientName) return ['success' => false, 'error' => "Row $rowNum: Client name is required"];
            if (!$origin) return ['success' => false, 'error' => "Row $rowNum: Origin is required"];
            if (!$destination) return ['success' => false, 'error' => "Row $rowNum: Destination is required"];

            // Generate tracking number if missing or invalid format
            if (!$trackingNumber || str_starts_with($trackingNumber, '=') || strlen($trackingNumber) < 5) {
                $trackingNumber = 'ZML-' . strtoupper(substr(uniqid(), -8));
            } else {
                // Convert scientific notation and clean the tracking number
                $trackingNumber = preg_replace('/\.0+E\+?(\d+)/i', 'ZML-$1', $trackingNumber);
                if (!str_starts_with(strtoupper($trackingNumber), 'ZML')) {
                    $trackingNumber = 'ZML-' . str_replace([' ', '-'], '', strtoupper($trackingNumber));
                }
            }
            if (!$origin) return ['success' => false, 'error' => "Row $rowNum: Origin is required"];
            if (!$destination) return ['success' => false, 'error' => "Row $rowNum: Destination is required"];

            // Find or create user/client
            $user = User::where('name', $clientName)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $clientName,
                    'email' => strtolower(str_replace(' ', '.', $clientName)) . '@client.local',
                    'password' => bcrypt('temp' . uniqid()),
                    'is_admin' => false,
                ]);
            }

            // Check if tracking number already exists, regenerate if so
            $baseTracking = $trackingNumber;
            $counter = 0;
            while (Shipment::where('tracking_number', $trackingNumber)->exists()) {
                $counter++;
                $trackingNumber = $baseTracking . '-' . $counter;
            }

            // Create shipment
            $shipment = Shipment::create([
                'user_id' => $user->id,
                'client_name' => $clientName,
                'tracking_number' => $trackingNumber,
                'serial_no' => $serialNumber ?: null,
                'origin' => $origin,
                'destination' => $destination,
                'service' => $cargoType ?: 'General Cargo',
                'weight' => $weight > 0 ? $weight : null,
                'quantity' => $quantity > 0 ? $quantity : null,
                'status' => $status ?: 'Order Placed',
                'cost' => $cost > 0 ? $cost : 0,
                'driver' => $driver ?: null,
                'vehicle_registration' => $vehicleReg ?: null,
                'estimated_delivery' => $estimatedDelivery ? $this->parseDate($estimatedDelivery) : null,
            ]);

            return [
                'success' => true,
                'shipment' => [
                    'tracking' => $trackingNumber,
                    'client' => $clientName,
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight > 0 ? $weight . ' KG' : 'N/A',
                ]
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Row $rowNum: {$e->getMessage()}"];
        }
    }

    private function parseDate($date)
    {
        if (!$date) return null;
        $date = trim($date);
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/Y H:i', 'd/m/Y H:i:s'];
        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                continue;
            }
        }
        return null;
    }

    public function downloadTemplate()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $filename = 'shipment_bulk_import_template.csv';
        $headers = ['Client Name', 'Tracking Number', 'Serial Number', 'Origin', 'Destination', 'Cargo Type', 'Weight (KG)', 'Quantity', 'Status', 'Cost (ZMW)', 'Estimated Delivery', 'Driver', 'Vehicle Registration', 'Cargo Images'];
        $handle = fopen('php://memory', 'w');
        fputcsv($handle, $headers);

        // Add sample rows
        $sample1 = ['ABC Mining Ltd', 'SF0229977546080', 'RS.26060632', 'China (Guangzhou Port)', 'Lusaka, Zambia', 'General Cargo', '500', '5', 'Order Placed', '15500.00', '15/07/2026', 'John Banda', 'ZMK-123-ABC', 'image1.jpg'];
        fputcsv($handle, $sample1);

        $sample2 = ['XYZ Traders Ltd', '703003614399', 'RS.26061031', 'China (Guangzhou Port)', 'Ndola, Zambia', 'Refrigerated Cargo', '250', '10', 'In Transit', '22000.00', '20/07/2026', 'James Mwale', 'ZMK-456-XYZ', 'image2.jpg'];
        fputcsv($handle, $sample2);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
