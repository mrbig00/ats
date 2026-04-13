<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('status', 50)->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('color', 50)->nullable();
            $table->string('calendar_itemable_type');
            $table->unsignedBigInteger('calendar_itemable_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['calendar_itemable_type', 'calendar_itemable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_items');
    }
};
