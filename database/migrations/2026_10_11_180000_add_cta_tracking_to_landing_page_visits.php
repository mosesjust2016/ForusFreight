<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('landing_page_visits', function (Blueprint $table) {
            $table->boolean('cta_clicked')->default(false)->after('bounced');
            $table->string('cta_type')->nullable()->after('cta_clicked');
        });
    }

    public function down(): void
    {
        Schema::table('landing_page_visits', fn (Blueprint $table) => $table->dropColumn(['cta_clicked', 'cta_type']));
    }
};
