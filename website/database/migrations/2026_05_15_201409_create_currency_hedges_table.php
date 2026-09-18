<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_hedges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount_usd', 15, 2);
            $table->decimal('hedged_rate', 12, 4);
            $table->decimal('amount_zmw', 15, 2);
            $table->date('hedge_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'expired', 'utilized', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_hedges');
    }
};
