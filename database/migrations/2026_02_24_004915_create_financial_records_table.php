<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('description');
            $table->string('supplier')->nullable();
            $table->string('pr_number')->nullable();
            $table->decimal('pr_amount',12,2)->nullable();
            $table->string('po_number')->nullable();
            $table->decimal('po_amount',12,2)->nullable();
            $table->string('obr_number')->nullable();
            $table->string('voucher_number')->nullable();
            $table->foreignId('office_origin')->constrained('offices');
            $table->foreignId('current_office')->nullable()->constrained('offices');
            $table->foreignId('current_holder')->nullable()->constrained('users');
            $table->enum('status',['ACTIVE','CANCELLED','FINISHED'])->default('ACTIVE');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};