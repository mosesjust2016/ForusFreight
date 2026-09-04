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
        Schema::create('deal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#4caf50');
            $table->integer('position')->default(0);
            $table->decimal('win_probability', 5, 2)->default(0); // 0-100%
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_won')->default(false);
            $table->timestamps();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deal_stage_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency')->default('ZMW');
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->string('source')->nullable(); // web, referral, campaign, etc.
            $table->string('priority')->default('medium'); // low, medium, high
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
        Schema::dropIfExists('deal_stages');
    }
};
