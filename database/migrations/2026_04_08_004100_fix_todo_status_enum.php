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
        // First update existing data to use hyphenated values
        DB::statement("UPDATE pictorts.todos SET status = 'on-going' WHERE status = 'ongoing'");
        
        // Then modify the column to use hyphenated values
        Schema::table('pictorts.todos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'on-going', 'done', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to non-hyphenated values
        DB::statement("UPDATE pictorts.todos SET status = 'ongoing' WHERE status = 'on-going'");
        
        Schema::table('pictorts.todos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'ongoing', 'done', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }
};
