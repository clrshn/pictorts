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
        Schema::table('todos', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('priority');
            
            // Re-add with all priority options including 'top'
            $table->enum('priority', ['low', 'medium', 'high', 'top'])->default('medium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // Revert to original enum without 'top'
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
        });
    }
};
