<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('landing_pages', fn (Blueprint $table) => $table->string('template')->default('freight_quote')->after('slug')); }
    public function down(): void { Schema::table('landing_pages', fn (Blueprint $table) => $table->dropColumn('template')); }
};
