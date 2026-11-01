<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('service_type');
            $table->string('full_name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('pickup');
            $table->string('delivery');
            $table->decimal('weight', 12, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
