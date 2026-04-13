<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nationality', 120)->nullable()->after('exit_date');
            $table->string('driving_license_category', 32)->nullable()->after('nationality');
            $table->boolean('has_own_car')->nullable()->after('driving_license_category');
            $table->string('german_level', 20)->nullable()->after('has_own_car');
            $table->date('available_from')->nullable()->after('german_level');
            $table->boolean('housing_needed')->nullable()->after('available_from');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'driving_license_category',
                'has_own_car',
                'german_level',
                'available_from',
                'housing_needed',
            ]);
        });
    }
};
