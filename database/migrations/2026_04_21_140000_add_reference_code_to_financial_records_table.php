<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            $table->string('reference_code')->nullable()->after('description');
        });

        DB::table('financial_records')
            ->select('id', 'created_at')
            ->orderBy('id')
            ->each(function ($record) {
                $year = $record->created_at
                    ? \Carbon\Carbon::parse($record->created_at)->format('Y')
                    : now()->format('Y');

                DB::table('financial_records')
                    ->where('id', $record->id)
                    ->update([
                        'reference_code' => sprintf('PICTO-FIN-%s-%06d', $year, $record->id),
                    ]);
            });

        Schema::table('financial_records', function (Blueprint $table) {
            $table->unique('reference_code');
        });
    }

    public function down(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            $table->dropUnique(['reference_code']);
            $table->dropColumn('reference_code');
        });
    }
};
