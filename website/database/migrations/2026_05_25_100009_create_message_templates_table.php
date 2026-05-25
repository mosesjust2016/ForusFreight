<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('channel')->default('whatsapp'); // sms, whatsapp, both
            $table->text('body');
            $table->string('category')->default('general'); // general, follow_up, promo, reminder, invoice, custom_clearance
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['channel', 'category']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
