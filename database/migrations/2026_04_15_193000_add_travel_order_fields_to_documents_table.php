<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('travel_order_type')->nullable()->after('delivery_scope');
            $table->string('travel_dates')->nullable()->after('travel_order_type');
            $table->text('travelers')->nullable()->after('travel_dates');
            $table->text('destinations')->nullable()->after('travelers');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'travel_order_type',
                'travel_dates',
                'travelers',
                'destinations',
            ]);
        });
    }
};
