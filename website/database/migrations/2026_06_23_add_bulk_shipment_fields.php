<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('weight', 10, 2)->nullable()->after('service');
            $table->integer('quantity')->nullable()->after('weight');
            $table->string('driver')->nullable()->after('quantity');
            $table->string('vehicle_registration')->nullable()->after('driver');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['weight', 'quantity', 'driver', 'vehicle_registration']);
        });
    }
};
