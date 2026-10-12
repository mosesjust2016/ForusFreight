<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pacra_entity_id')->nullable()->index();
            $table->date('pacra_registration_date')->nullable();
            $table->string('pacra_entity_type')->nullable();
            $table->string('pacra_company_type')->nullable();
            $table->string('pacra_entity_limit')->nullable();
            $table->string('pacra_isic_classification')->nullable();
            $table->text('pacra_isic_description')->nullable();
            $table->json('pacra_record')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'pacra_entity_id', 'pacra_registration_date', 'pacra_entity_type',
                'pacra_company_type', 'pacra_entity_limit', 'pacra_isic_classification',
                'pacra_isic_description', 'pacra_record',
            ]);
        });
    }
};
