<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Deduplicate serial_no values - find duplicates and make them unique
        $duplicates = DB::table('shipments')
            ->select('serial_no', DB::raw('count(*) as cnt'))
            ->whereNotNull('serial_no')
            ->where('serial_no', '!=', '')
            ->groupBy('serial_no')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Get all rows with this duplicate serial_no, ordered by id
            $rows = DB::table('shipments')
                ->where('serial_no', $dup->serial_no)
                ->orderBy('id')
                ->get();

            // Keep the first one, generate new serial_no for the rest
            $first = true;
            foreach ($rows as $row) {
                if ($first) {
                    $first = false;
                    continue;
                }
                
                $newSerialNo = $dup->serial_no . '-' . $row->id;
                DB::table('shipments')->where('id', $row->id)->update(['serial_no' => $newSerialNo]);
            }
        }

        // Step 2: Ensure all rows have a serial_no (backfill from tracking_number if needed)
        $missing = DB::table('shipments')
            ->whereNull('serial_no')
            ->orWhere('serial_no', '')
            ->get();

        foreach ($missing as $shipment) {
            $serialNo = $shipment->tracking_number;
            
            if (!$serialNo || DB::table('shipments')->where('serial_no', $serialNo)->exists()) {
                $serialNo = 'ZML-' . strtoupper(substr(uniqid(), -8));
                while (DB::table('shipments')->where('serial_no', $serialNo)->exists()) {
                    $serialNo = 'ZML-' . strtoupper(substr(uniqid(), -8));
                }
            }
            
            DB::table('shipments')->where('id', $shipment->id)->update(['serial_no' => $serialNo]);
        }

        // Step 3: Now make serial_no non-nullable and unique
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('serial_no')->nullable(false)->unique()->change();
            $table->string('tracking_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('serial_no')->nullable()->unique(false)->change();
            $table->string('tracking_number')->nullable(false)->unique()->change();
        });
    }
};
