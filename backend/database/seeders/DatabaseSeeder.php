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
        ]);

        \Illuminate\Support\Facades\DB::table('tour_dates')->insert([
            ['tour_id' => 1, 'date' => '2026-04-10', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['tour_id' => 1, 'date' => '2026-04-15', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['tour_id' => 2, 'date' => '2026-05-01', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
            ['tour_id' => 2, 'date' => '2026-05-10', 'status' => 'Disabled', 'created_at' => now(), 'updated_at' => now()],
            ['tour_id' => 3, 'date' => '2026-06-20', 'status' => 'Enabled', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
