<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pacra_entity_number')->nullable()->index();
            $table->string('pacra_entity_status')->nullable();
            $table->string('pacra_business_nature')->nullable();
            $table->timestamp('pacra_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['pacra_entity_number', 'pacra_entity_status', 'pacra_business_nature', 'pacra_verified_at']);
        });
    }
};
