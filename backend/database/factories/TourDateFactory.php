<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourDate>
 */
class TourDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => \App\Models\Tour::factory(),
            'date'    => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status'  => \App\Models\TourDate::STATUS_ENABLED,
        ];
    }
}
