<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('date');
        });

        // Initialize existing rows end_date to be same as date
        DB::table('tour_dates')->whereNull('end_date')->update([
            'end_date' => DB::raw('date')
        ]);
    }

    public function down(): void
    {
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
