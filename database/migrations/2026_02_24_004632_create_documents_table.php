<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('dts_number')->unique();
            $table->string('document_type');
            $table->enum('direction',['INCOMING','OUTGOING']);
            $table->foreignId('originating_office')->constrained('offices');
            $table->foreignId('current_office')->nullable()->constrained('offices');
            $table->foreignId('current_holder')->nullable()->constrained('users');
            $table->string('subject');
            $table->text('action_required')->nullable();
            $table->date('date_received')->nullable();
            $table->enum('status',['ONGOING','DELIVERED','COMPLETED'])->default('ONGOING');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};