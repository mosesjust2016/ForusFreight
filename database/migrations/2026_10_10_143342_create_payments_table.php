<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Our referenceId, sent to Lipila and echoed back in callbacks.
            $table->string('reference_id')->unique();
            // Lipila's internal identifier (e.g. LPLXC-20260709-...).
            $table->string('identifier')->nullable();
            $table->string('method'); // card | momo
            $table->string('account_number')->nullable(); // phone/card identifier charged
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status')->default('pending'); // pending | successful | failed
            $table->string('payment_type')->nullable(); // Card | MtnMoney | AirtelMoney | ...
            $table->string('external_id')->nullable();  // MNO transaction id
            $table->string('message')->nullable();      // gateway's last message
            $table->string('webhook_id')->nullable();   // last processed webhook (idempotency)
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
