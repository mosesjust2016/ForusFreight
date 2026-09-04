<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('from', 'origin');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('to', 'destination');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->timestamp('shipment_date')->nullable()->after('status');
            $table->timestamp('estimated_delivery')->nullable()->after('service');
        });
    }

    public function down(): void {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('shipment_date');
            $table->dropColumn('estimated_delivery');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('destination', 'to');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('origin', 'from');
        });
    }
};
