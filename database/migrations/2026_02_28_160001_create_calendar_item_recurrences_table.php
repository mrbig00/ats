<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_item_recurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->cascadeOnDelete();
            $table->text('rrule');
            $table->string('timezone', 100);
            $table->timestamp('dtstart');
            $table->timestamp('until')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->json('exceptions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_item_recurrences');
    }
};
