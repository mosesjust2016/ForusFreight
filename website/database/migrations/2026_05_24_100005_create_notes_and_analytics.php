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
        Schema::create('contact_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('note'); // note, call, email, meeting, purchase, preference
            $table->text('content');
            $table->json('metadata')->nullable(); // store extra data like call duration, email subject, etc.
            $table->timestamps();
        });

        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->integer('total_contacts')->default(0);
            $table->integer('total_deals')->default(0);
            $table->integer('total_tasks')->default(0);
            $table->integer('open_tickets')->default(0);
            $table->decimal('pipeline_value', 15, 2)->default(0);
            $table->decimal('revenue_forecast', 15, 2)->default(0);
            $table->decimal('won_revenue', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
        Schema::dropIfExists('contact_notes');
    }
};
