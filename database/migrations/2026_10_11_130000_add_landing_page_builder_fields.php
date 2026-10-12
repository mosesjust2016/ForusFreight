<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('hero_title')->nullable();
            $table->text('hero_text')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->string('cta_label')->default('Get a quote');
            $table->string('cta_url')->nullable();
            $table->string('form_title')->nullable();
            $table->text('form_description')->nullable();
            $table->string('primary_color')->default('#ff6200');
            $table->string('accent_color')->default('#007f7f');
        });
    }
    public function down(): void {
        Schema::table('landing_pages', fn (Blueprint $table) => $table->dropColumn(['hero_title','hero_text','hero_image_url','cta_label','cta_url','form_title','form_description','primary_color','accent_color']));
    }
};
