<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 64);
            $table->string('subject_type', 128)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('occurred_at');
            $table->json('meta')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('activity_events', function (Blueprint $table) {
            $table->index(['type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_events');
    }
};
