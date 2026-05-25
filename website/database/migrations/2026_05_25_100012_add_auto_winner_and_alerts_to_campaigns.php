<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->integer('alert_threshold')->default(0)->after('is_ab_test');
            $table->boolean('auto_send_winner')->default(false);
            $table->integer('winner_decision_min_replies')->default(30);
            $table->integer('winner_decision_delay_hours')->default(24);
            $table->string('winner_variant')->nullable();
            $table->timestamp('winner_declared_at')->nullable();
        });

        Schema::create('campaign_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('whatsapp_campaigns')->cascadeOnDelete();
            $table->string('type');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['campaign_id', 'type']);
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'alert_threshold', 'auto_send_winner', 'winner_decision_min_replies',
                'winner_decision_delay_hours', 'winner_variant', 'winner_declared_at'
            ]);
        });

        Schema::dropIfExists('campaign_alerts');
    }
};
