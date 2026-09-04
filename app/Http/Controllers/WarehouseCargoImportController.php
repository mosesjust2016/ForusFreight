<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WarehouseCargo;
use App\Models\User;
use Carbon\Carbon;

class WarehouseCargoImportController extends Controller
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
        return view('admin.warehouse-cargo-import');
    }

    public function import(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $results = ['success' => 0, 'failed' => 0, 'errors' => [], 'cargo' => []];

        try {
            $rows = $this->parseFile($file);
            
            if (empty($rows)) {
                return back()->with('error', 'File is empty or invalid format.');
            }

            foreach ($rows as $rowNum => $row) {
                $rowNum++;
                
                if (empty(array_filter($row))) continue;

                $result = $this->createCargoFromRow($row, $rowNum);
                
                if ($result['success']) {
                    $results['success']++;
                    $results['cargo'][] = $result['cargo'];
                } else {
                    $results['failed']++;
                    $results['errors'][] = $result['error'];
                }
            }

            return back()->with('import_results', $results)
                        ->with('success', "Import completed. {$results['success']} cargo entries created, {$results['failed']} failed.");
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
        } elseif (str_ends_with($filename, '.xlsx')) {
            return $this->parseXLSX($path);
        } elseif (str_ends_with($filename, '.xls')) {
            return $this->parseXLS($path);
        }
        return [];
    }

    private function parseCSV($path)
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Skip first header row
            fgetcsv($handle);
            // Skip duplicate header row if exists
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function parseXLSX($path)
    {
        $rows = [];
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
                $zip->close();

                $headerSkipped = 0;
                foreach ($xml->sheetData->row as $row) {
                    // Skip first two header rows
                    if ($headerSkipped < 2) {
                        $headerSkipped++;
                        continue;
                    }

                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $rowData[] = (string)$cell->v;
                    }
                    if (!empty(array_filter($rowData))) {
                        $rows[] = $rowData;
                    }
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $rows;
    }

    private function parseXLS($path)
    {
        // For .xls files, try to convert to CSV or read using basic method
        // Since .xls is binary format, we'll try a simple approach
        return $this->parseXLSX($path); // This won't work for true XLS, but returns empty on error
    }

    private function createCargoFromRow($row, $rowNum)
    {
        try {
            // Map columns
            $inventoryNumber = trim($row[0] ?? '');
            $warehouseNumber = trim($row[1] ?? '');
            $entryDate = trim($row[2] ?? '');
            $customerCode = trim($row[3] ?? '');
            $receiverName = trim($row[4] ?? '');
            $receiverPhone = trim($row[5] ?? '');
            $cargoNameCN = trim($row[6] ?? '');
            $cargoNameEN = trim($row[7] ?? '');
            $cartons = (int)($row[8] ?? 1);
            $weight = (float)preg_replace('/[^0-9.]/', '', $row[9] ?? '0');
            $volume = (float)preg_replace('/[^0-9.]/', '', $row[10] ?? '0');
            $driverInfo = trim($row[11] ?? '');

            // Validate required fields
            if (!$inventoryNumber) return ['success' => false, 'error' => "Row $rowNum: Inventory number is required"];
            if (!$warehouseNumber) return ['success' => false, 'error' => "Row $rowNum: Warehouse entry number is required"];
            if (!$cargoNameEN) return ['success' => false, 'error' => "Row $rowNum: Cargo name (English) is required"];

            // Generate tracking number with ZML prefix
            $trackingNumber = 'ZML-' . str_replace(' ', '', $warehouseNumber);

            // Check if already exists
            $existing = WarehouseCargo::where('inventory_number', $inventoryNumber)->first();
            if ($existing) {
                return ['success' => false, 'error' => "Row $rowNum: Inventory number $inventoryNumber already exists"];
            }

            // Find user by customer code (e.g., ZMFFL)
            $user = User::where('name', 'LIKE', '%' . $customerCode . '%')->first();

            // Create cargo entry
            $cargo = WarehouseCargo::create([
                'inventory_number' => $inventoryNumber,
                'warehouse_entry_number' => $warehouseNumber,
                'entry_date' => $entryDate ? Carbon::createFromFormat('Y-m-d', $entryDate) : now(),
                'customer_code' => $customerCode,
                'user_id' => $user?->id,
                'receiver_name' => $receiverName ?: null,
                'receiver_phone' => $receiverPhone ?: null,
                'cargo_name_chinese' => $cargoNameCN ?: null,
                'cargo_name_english' => $cargoNameEN,
                'cartons' => $cartons > 0 ? $cartons : 1,
                'gross_weight' => $weight > 0 ? $weight : null,
                'volume' => $volume > 0 ? $volume : null,
                'driver_info' => $driverInfo ?: null,
                'tracking_number' => $trackingNumber,
                'status' => 'In Warehouse',
            ]);

            return [
                'success' => true,
                'cargo' => [
                    'inventory' => $inventoryNumber,
                    'tracking' => $trackingNumber,
                    'cargo' => $cargoNameEN,
                    'customer' => $customerCode,
                    'receiver' => $receiverName ?: 'N/A',
                    'weight' => $weight > 0 ? $weight . ' KG' : 'N/A',
                ]
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Row $rowNum: {$e->getMessage()}"];
        }
    }

    public function downloadTemplate()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $filename = 'warehouse_cargo_import_template.csv';
        $headers = ['库存编号', '入仓流水号', '入仓日期', '客户代码', '收货人姓名', '收货人电话', '总货名', 'Total Cargo Name', '总箱数', '总毛重', '总体积', '司机信息'];
        $handle = fopen('php://memory', 'w');
        fputcsv($handle, $headers);

        // Add sample rows
        $sample1 = ['33067', 'RS.26061987', '2026-06-17', 'ZMFFL', 'TISA JOBO', '0972936355', '衣服', 'Clothes', '6', '470', '2.4', ''];
        fputcsv($handle, $sample1);

        $sample2 = ['33049', 'RS.26061969', '2026-06-17', 'ZMFFL', 'Customer Name', '0972000000', '美的冰箱', 'Midea Refrigerator', '1', '90.4', '1.19', 'JDLD13114110095'];
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
