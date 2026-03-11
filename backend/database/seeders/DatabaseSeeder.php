<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('tours')->insert([
            ['id' => 1, 'name' => 'Ha Long Bay Adventure', 'description' => 'A wonderful 3-day trip to Ha Long Bay.', 'status' => 'Public', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Sapa Trekking Tour', 'description' => 'Enjoy the beautiful mountains in Sapa.', 'status' => 'Public', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Mekong Delta Explorer', 'description' => 'Discover the floating markets.', 'status' => 'Draft', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Old City Walk', 'description' => 'Discover the old city.', 'status' => 'Public', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \Illuminate\Support\Facades\DB::table('tour_dates')->insert([
            ['id' => 1, 'tour_id' => 1, 'date' => '2026-04-10', 'end_date' => '2026-04-12', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tour_id' => 1, 'date' => '2026-04-15', 'end_date' => '2026-04-17', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'tour_id' => 2, 'date' => '2026-05-01', 'end_date' => '2026-05-01', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'tour_id' => 2, 'date' => '2026-05-10', 'end_date' => '2026-05-12', 'status' => 'Disabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'tour_id' => 3, 'date' => '2026-06-20', 'end_date' => '2026-06-21', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'tour_id' => 4, 'date' => '2023-01-01', 'end_date' => '2023-01-01', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()], // Expired
        ]);

        \Illuminate\Support\Facades\DB::table('bookings')->insert([
            ['id' => 1, 'tour_id' => 1, 'tour_date_id' => 1, 'customer_name' => 'John Doe', 'customer_email' => 'john@example.com', 'status' => 'Confirmed', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tour_id' => 1, 'tour_date_id' => 2, 'customer_name' => 'Jane Smith', 'customer_email' => 'jane@example.com', 'status' => 'Submitted', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \Illuminate\Support\Facades\DB::table('passengers')->insert([
            ['id' => 1, 'given_name' => 'John', 'surname' => 'Doe', 'email' => 'john@example.com', 'phone' => '123456789', 'date_of_birth' => '1990-01-01', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'given_name' => 'Jane', 'surname' => 'Smith', 'email' => 'jane@example.com', 'phone' => '987654321', 'date_of_birth' => '1992-05-05', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'given_name' => 'Peter', 'surname' => 'Parker', 'email' => 'spiderman@example.com', 'phone' => '000111222', 'date_of_birth' => '2001-08-10', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'given_name' => 'Bruce', 'surname' => 'Wayne', 'email' => 'batman@example.com', 'phone' => '999888777', 'date_of_birth' => '1985-04-15', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'given_name' => 'Clark', 'surname' => 'Kent', 'email' => 'superman@example.com', 'phone' => '555444333', 'date_of_birth' => '1988-12-25', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'given_name' => 'Diana', 'surname' => 'Prince', 'email' => 'wonderwoman@example.com', 'phone' => '111222333', 'date_of_birth' => '1995-10-31', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'given_name' => 'Tony', 'surname' => 'Stark', 'email' => 'ironman@example.com', 'phone' => '444555666', 'date_of_birth' => '1980-05-29', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \Illuminate\Support\Facades\DB::table('booking_passenger')->insert([
            ['booking_id' => 1, 'passenger_id' => 1],
            ['booking_id' => 2, 'passenger_id' => 2],
        ]);

        \Illuminate\Support\Facades\DB::table('invoices')->insert([
            ['booking_id' => 1, 'amount' => 150.00, 'status' => 'Paid', 'created_at' => now(), 'updated_at' => now()],
            ['booking_id' => 2, 'amount' => 300.00, 'status' => 'Unpaid', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
