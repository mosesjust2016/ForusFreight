<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('crm_status')->default('lead')->after('whatsapp_number');
            $table->decimal('credit_limit', 15, 2)->default(0)->after('crm_status');
            $table->string('payment_terms')->default('prepaid')->after('credit_limit');
            $table->string('assigned_agent')->nullable()->after('payment_terms');
            $table->text('internal_notes')->nullable()->after('assigned_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['crm_status', 'credit_limit', 'payment_terms', 'assigned_agent', 'internal_notes']);
        });
    }
};
