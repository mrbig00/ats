<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_item_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->cascadeOnDelete();
            $table->timestamp('occurrence_date');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['calendar_item_id', 'occurrence_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_item_overrides');
    }
};
