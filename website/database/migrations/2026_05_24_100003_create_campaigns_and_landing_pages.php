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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // email, social, digital_ad, mixed
            $table->string('status')->default('draft'); // draft, active, paused, completed
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('spent', 15, 2)->default(0);
            $table->integer('leads_generated')->default(0);
            $table->integer('conversions')->default(0);
            $table->string('target_audience')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->string('campaign_source')->nullable(); // utm_source
            $table->string('campaign_medium')->nullable(); // utm_medium
            $table->integer('views')->default(0);
            $table->integer('submissions')->default(0);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('campaigns');
    }
};
