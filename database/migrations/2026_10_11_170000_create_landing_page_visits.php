<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('landing_page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained()->cascadeOnDelete();
            $table->string('source')->nullable();
            $table->string('medium')->nullable();
            $table->string('campaign')->nullable();
            $table->string('session_key')->index();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->boolean('bounced')->default(true);
            $table->timestamps();
            $table->index(['landing_page_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_visits');
    }
};
