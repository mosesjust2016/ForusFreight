<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('dial_code', 10);
            $table->string('flag', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('phone_countries')->insert([
            ['name' => 'Zambia',       'dial_code' => '260', 'flag' => '🇿🇲', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'China',        'dial_code' => '86',  'flag' => '🇨🇳', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'South Africa', 'dial_code' => '27',  'flag' => '🇿🇦', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zimbabwe',     'dial_code' => '263', 'flag' => '🇿🇼', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_countries');
    }
};
