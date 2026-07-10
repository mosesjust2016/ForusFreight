<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_cargo', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_number')->unique();
            $table->string('warehouse_entry_number');
            $table->date('entry_date');
            $table->string('customer_code');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('cargo_name_chinese')->nullable();
            $table->string('cargo_name_english');
            $table->integer('cartons')->default(1);
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->string('driver_info')->nullable();
            $table->string('tracking_number')->unique();
            $table->string('status')->default('In Warehouse');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('customer_code');
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_cargo');
    }
};
