<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->unsignedInteger('capacity')->default(10); // Default to 10 for existing ones
        });
    }

    public function down(): void
    {
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
};
