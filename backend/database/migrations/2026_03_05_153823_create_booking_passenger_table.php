<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_passenger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained()->cascadeOnDelete();

            // Allow special request at the booking-passenger level
            $table->text('special_request')->nullable();

            $table->timestamps();

            // Prevent duplicate passengers on the same booking
            $table->unique(['booking_id', 'passenger_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_passenger');
    }
};
