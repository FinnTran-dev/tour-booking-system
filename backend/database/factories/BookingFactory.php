<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id'        => \App\Models\Tour::factory(),
            'tour_date_id'   => \App\Models\TourDate::factory(),
            'customer_name'  => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'status'         => \App\Models\Booking::STATUS_SUBMITTED,
        ];
    }
}
