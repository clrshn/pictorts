<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_id')->constrained('financial_records')->cascadeOnDelete();
            $table->foreignId('from_office')->constrained('offices');
            $table->foreignId('to_office')->constrained('offices');
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('datetime_released')->nullable();
            $table->timestamp('datetime_received')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_routes');
    }
};