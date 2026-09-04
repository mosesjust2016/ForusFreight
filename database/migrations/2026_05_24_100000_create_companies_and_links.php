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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Zambia');
            $table->string('tax_id')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->integer('employee_count')->nullable();
            $table->string('status')->default('active'); // active, inactive, prospect
            $table->text('notes')->nullable();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('contact'); // contact, manager, decision_maker
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->integer('lead_score')->default(0)->after('company_id');
            $table->timestamp('last_engagement_at')->nullable()->after('lead_score');
            $table->json('preferences')->nullable()->after('last_engagement_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'lead_score', 'last_engagement_at', 'preferences']);
        });

        Schema::dropIfExists('company_user');
        Schema::dropIfExists('companies');
    }
};
