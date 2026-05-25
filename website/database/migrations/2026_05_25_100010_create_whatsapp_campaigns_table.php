<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('opted_out_count')->default(0);
            $table->string('status')->default('draft'); // draft, queued, sending, paused, completed, cancelled
            $table->integer('delay_min')->default(5);  // minimum seconds between messages
            $table->integer('delay_max')->default(15); // maximum seconds between messages
            $table->integer('daily_limit')->default(300); // max messages per day
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'status']);
        });

        Schema::create('whatsapp_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('whatsapp_campaigns')->cascadeOnDelete();
            $table->string('phone');
            $table->string('name')->nullable();
            $table->string('status')->default('pending'); // pending, processing, sent, failed, opted_out, invalid
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaign_recipients');
        Schema::dropIfExists('whatsapp_campaigns');
    }
};
