<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_item_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->cascadeOnDelete();
            $table->timestamp('exception_date');
            $table->timestamps();

            $table->unique(['calendar_item_id', 'exception_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_item_exceptions');
    }
};
