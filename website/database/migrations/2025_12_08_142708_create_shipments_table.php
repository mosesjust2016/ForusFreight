<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->unique();
            $table->string('status')->default('Pending');
            $table->string('from');
            $table->string('to');
            $table->string('service')->nullable();
            $table->json('history')->nullable(); // array of updates
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};
