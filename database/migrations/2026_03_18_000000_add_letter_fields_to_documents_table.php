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
            $table->text('opg_reference_no')->nullable()->after('particulars');
            $table->text('opa_reference_no')->nullable()->after('opg_reference_no');
            $table->text('governors_instruction')->nullable()->after('opa_reference_no');
            $table->text('administrators_instruction')->nullable()->after('governors_instruction');
            $table->text('returned')->nullable()->after('administrators_instruction');
            $table->text('opg_action_slip')->nullable()->after('returned');
            $table->text('dts_no')->nullable()->after('opg_action_slip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('opg_reference_no');
            $table->dropColumn('opa_reference_no');
            $table->dropColumn('governors_instruction');
            $table->dropColumn('administrators_instruction');
            $table->dropColumn('returned');
            $table->dropColumn('opg_action_slip');
            $table->dropColumn('dts_no');
        });
    }
};
