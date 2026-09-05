<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('next_action')->nullable()->after('current_border');
            $table->dateTime('delivery_date')->nullable()->after('estimated_delivery');
            $table->string('proof_of_delivery')->nullable()->after('delivery_date');
        });

        // event_time stays NOT NULL (altering it on SQLite forces a table
        // rebuild that risks data loss under FK constraints). The importer
        // synthesizes a value when a CSV row has no explicit date instead.
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->integer('sequence')->nullable()->after('event_time');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['next_action', 'delivery_date', 'proof_of_delivery']);
        });

        Schema::table('tracking_events', function (Blueprint $table) {
            $table->dropColumn('sequence');
        });
    }
};
