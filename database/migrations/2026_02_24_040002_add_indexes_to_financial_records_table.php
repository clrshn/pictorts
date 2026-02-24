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
        Schema::table('financial_records', function (Blueprint $table) {
            $table->index('type');
            $table->index('status');
            $table->index('office_origin');
            $table->index('current_office');
            $table->index('current_holder');
            $table->index('pr_number');
            $table->index('po_number');
            $table->index('obr_number');
            $table->index('voucher_number');
            $table->index(['status', 'office_origin']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['office_origin']);
            $table->dropIndex(['current_office']);
            $table->dropIndex(['current_holder']);
            $table->dropIndex(['pr_number']);
            $table->dropIndex(['po_number']);
            $table->dropIndex(['obr_number']);
            $table->dropIndex(['voucher_number']);
            $table->dropIndex(['status', 'office_origin']);
            $table->dropIndex(['created_at']);
        });
    }
};
