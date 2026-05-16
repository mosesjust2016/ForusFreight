<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3)->default('USD');
            $table->string('quote_currency', 3)->default('ZMW');
            $table->decimal('buying_rate', 12, 4);
            $table->decimal('mid_rate', 12, 4);
            $table->decimal('selling_rate', 12, 4);
            $table->string('source')->default('boz');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
