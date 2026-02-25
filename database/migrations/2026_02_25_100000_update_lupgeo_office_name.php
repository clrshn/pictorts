<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('offices')
            ->where('code', 'LUPGEO')
            ->update(['name' => 'La Union Provincial Government Engineering Office']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('offices')
            ->where('code', 'LUPGEO')
            ->update(['name' => 'La Union Provincial Government Engineering Office (Others)']);
    }
};
