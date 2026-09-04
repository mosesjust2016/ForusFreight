<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('user_id');
            $table->string('serial_no')->nullable()->after('tracking_number');
            $table->string('code')->nullable()->after('serial_no');
            $table->string('client_phone')->nullable()->after('client_name');
            $table->string('service_type')->nullable()->after('service');
            $table->date('date_of_load')->nullable()->after('shipment_date');
            $table->integer('no_of_parcels')->nullable()->after('quantity');
            $table->decimal('cbm_volume', 10, 2)->nullable()->after('no_of_parcels');
            $table->decimal('gross_weight', 10, 2)->nullable()->after('cbm_volume');
            $table->string('shipping_method')->nullable()->after('gross_weight');
            $table->string('port_in_china')->nullable()->after('origin');
            $table->string('port_destination')->nullable()->after('destination');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'client_name',
                'serial_no',
                'code',
                'client_phone',
                'date_of_load',
                'no_of_parcels',
                'cbm_volume',
                'gross_weight',
                'shipping_method',
                'port_in_china',
                'port_destination',
            ]);
        });
    }
};
