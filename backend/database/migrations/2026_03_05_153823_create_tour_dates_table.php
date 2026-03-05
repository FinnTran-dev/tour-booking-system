<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled')->index();
            $table->timestamps();

            // A tour cannot have the same date twice
            $table->unique(['tour_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_dates');
    }
};
