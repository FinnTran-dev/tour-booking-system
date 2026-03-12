<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourUpdateStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_tour_status_from_draft_to_public()
    {
        $tour = Tour::create([
            'name' => 'Initial Tour',
            'description' => 'A description',
            'status' => Tour::STATUS_DRAFT,
        ]);

        $response = $this->putJson("/api/v1/tours/{$tour->id}", [
            'name' => 'Updated Tour',
            'status' => Tour::STATUS_PUBLIC,
            'last_updated_at' => $tour->updated_at->toIso8601String(),
            'dates' => []
        ]);

        $response->assertStatus(200);
        $this->assertEquals(Tour::STATUS_PUBLIC, Tour::find($tour->id)->status);
    }
}
