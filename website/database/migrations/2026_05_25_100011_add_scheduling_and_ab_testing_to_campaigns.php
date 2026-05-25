<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('status');
            $table->boolean('is_ab_test')->default(false)->after('scheduled_at');
            $table->text('variant_a_message')->nullable()->after('is_ab_test');
            $table->text('variant_b_message')->nullable()->after('variant_a_message');
            $table->integer('split_percent')->default(50)->after('variant_b_message'); // % that gets variant A
            $table->integer('variant_a_sent')->default(0)->after('split_percent');
            $table->integer('variant_b_sent')->default(0)->after('variant_a_sent');
        });

        Schema::table('whatsapp_campaign_recipients', function (Blueprint $table) {
            $table->string('variant')->nullable()->after('name'); // 'a' or 'b'
            $table->timestamp('replied_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'is_ab_test', 'variant_a_message', 'variant_b_message', 'split_percent', 'variant_a_sent', 'variant_b_sent']);
        });

        Schema::table('whatsapp_campaign_recipients', function (Blueprint $table) {
            $table->dropColumn(['variant', 'replied_at']);
        });
    }
};
