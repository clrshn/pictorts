<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE documents MODIFY status ENUM('ONGOING','DELIVERED','COMPLETED','DONE') NOT NULL DEFAULT 'ONGOING'");
            DB::table('documents')->where('status', 'COMPLETED')->update(['status' => 'DONE']);
            DB::statement("ALTER TABLE documents MODIFY status ENUM('ONGOING','DELIVERED','DONE') NOT NULL DEFAULT 'ONGOING'");
        } elseif ($driver === 'sqlite') {
            DB::table('documents')->where('status', 'COMPLETED')->update(['status' => 'DONE']);
        }

        DB::table('document_routes')
            ->where('remarks', 'like', '%COMPLETED%')
            ->update([
                'remarks' => DB::raw("REPLACE(remarks, 'COMPLETED', 'DONE')")
            ]);
    }

    public function down(): void
    {
        DB::table('document_routes')
            ->where('remarks', 'like', '%DONE%')
            ->update([
                'remarks' => DB::raw("REPLACE(remarks, 'DONE', 'COMPLETED')")
            ]);

        DB::table('documents')->where('status', 'DONE')->update(['status' => 'COMPLETED']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE documents MODIFY status ENUM('ONGOING','DELIVERED','COMPLETED') NOT NULL DEFAULT 'ONGOING'");
        }
    }
};
