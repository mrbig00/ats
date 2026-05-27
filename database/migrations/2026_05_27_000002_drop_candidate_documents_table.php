<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('candidate_documents');
    }

    public function down(): void
    {
        // Legacy table removed; restore from 2026_02_15_180708_create_candidate_documents_table if needed.
    }
};
