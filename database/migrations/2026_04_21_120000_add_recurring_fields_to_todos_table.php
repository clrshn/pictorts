<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('remarks');
            $table->string('recurrence_frequency')->nullable()->after('is_recurring');
            $table->unsignedInteger('recurrence_interval')->nullable()->after('recurrence_frequency');
            $table->date('recurrence_end_date')->nullable()->after('recurrence_interval');
            $table->foreignId('recurring_parent_id')->nullable()->after('recurrence_end_date')->constrained('todos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recurring_parent_id');
            $table->dropColumn([
                'is_recurring',
                'recurrence_frequency',
                'recurrence_interval',
                'recurrence_end_date',
            ]);
        });
    }
};
