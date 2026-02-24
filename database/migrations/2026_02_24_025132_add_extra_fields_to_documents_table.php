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
            $table->string('ictu_number')->nullable()->after('dts_number');
            $table->string('doc_number')->nullable()->after('ictu_number');
            $table->foreignId('to_office')->nullable()->after('originating_office')->constrained('offices');
            $table->string('endorsed_to')->nullable()->after('action_required');
            $table->string('shared_drive_link')->nullable()->after('remarks');
            $table->boolean('received_via_online')->default(false)->after('shared_drive_link');
            $table->foreignId('encoded_by')->nullable()->after('received_via_online')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['to_office']);
            $table->dropForeign(['encoded_by']);
            $table->dropColumn(['ictu_number', 'doc_number', 'to_office', 'endorsed_to', 'shared_drive_link', 'received_via_online', 'encoded_by']);
        });
    }
};
