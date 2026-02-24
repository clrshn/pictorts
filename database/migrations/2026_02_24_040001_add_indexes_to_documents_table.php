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
        Schema::table('documents', function (Blueprint $table) {
            $table->index('dts_number');
            $table->index('ictu_number');
            $table->index('doc_number');
            $table->index('document_type');
            $table->index('direction');
            $table->index('status');
            $table->index('originating_office');
            $table->index('current_office');
            $table->index('current_holder');
            $table->index('encoded_by');
            $table->index(['document_type', 'status']);
            $table->index(['direction', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['dts_number']);
            $table->dropIndex(['ictu_number']);
            $table->dropIndex(['doc_number']);
            $table->dropIndex(['document_type']);
            $table->dropIndex(['direction']);
            $table->dropIndex(['status']);
            $table->dropIndex(['originating_office']);
            $table->dropIndex(['current_office']);
            $table->dropIndex(['current_holder']);
            $table->dropIndex(['encoded_by']);
            $table->dropIndex(['document_type', 'status']);
            $table->dropIndex(['direction', 'status']);
            $table->dropIndex(['created_at']);
        });
    }
};
