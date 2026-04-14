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
        // Fresh installs already create the column with the final enum values.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to avoid destructive schema changes on existing installations.
    }
};
