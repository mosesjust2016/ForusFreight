<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('communications_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel'); // sms, whatsapp
            $table->string('direction'); // outgoing, incoming
            $table->string('recipient_phone')->nullable();
            $table->string('sender_phone')->nullable();
            $table->text('message');
            $table->string('status')->default('sent'); // sent, delivered, failed, received, opted_out
            $table->string('external_id')->nullable(); // message id from provider
            $table->json('metadata')->nullable(); // provider response, error details
            $table->timestamps();

            $table->index(['channel', 'direction', 'created_at']);
            $table->index('recipient_phone');
        });

        Schema::create('communication_opt_outs', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('channel'); // sms, whatsapp
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('opted_out_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_opt_outs');
        Schema::dropIfExists('communications_logs');
    }
};
