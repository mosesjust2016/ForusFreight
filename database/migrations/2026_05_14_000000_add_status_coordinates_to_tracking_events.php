<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->string('status')->nullable()->after('description');
            $table->decimal('latitude', 10, 7)->nullable()->after('status');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->dropColumn(['status', 'latitude', 'longitude']);
        });
    }
};
