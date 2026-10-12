<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('landing_pages', function (Blueprint $table) { $table->boolean('show_form')->default(true); $table->json('form_fields')->nullable(); }); }
    public function down(): void { Schema::table('landing_pages', fn (Blueprint $table) => $table->dropColumn(['show_form','form_fields'])); }
};
