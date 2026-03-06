<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    private function publicTourWithDate(): array
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tourDate = TourDate::factory()->create([
            'tour_id' => $tour->id,
            'status'  => TourDate::STATUS_ENABLED,
            'date'    => now()->addDays(10)->toDateString(),
        ]);
        return [$tour, $tourDate];
    }

    /** @test */
    public function it_creates_a_booking_with_201(): void
    {
        [$tour, $tourDate] = $this->publicTourWithDate();

        $payload = [
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Feature Tester',
            'customer_email' => 'feature@example.com',
            'passengers'     => [
                ['given_name' => 'Alice', 'surname' => 'Smith', 'date_of_birth' => '1992-06-01'],
            ],
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.status', Booking::STATUS_SUBMITTED)
            ->assertJsonPath('data.invoice.status', Invoice::STATUS_UNPAID);

        $this->assertDatabaseHas('bookings', ['customer_email' => 'feature@example.com']);
        $this->assertDatabaseHas('invoices', ['amount' => 100.00, 'status' => 'Unpaid']);
    }

    /** @test */
    public function it_returns_400_when_booking_a_draft_tour(): void
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_DRAFT]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour->id, 'status' => TourDate::STATUS_ENABLED]);

        $response = $this->postJson('/api/v1/bookings', [
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.com',
            'passengers'     => [['given_name' => 'John', 'surname' => 'Doe', 'date_of_birth' => '1990-01-01']],
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Cannot book a tour that is not Public.']);
    }

    /** @test */
    public function it_returns_422_when_passengers_missing(): void
    {
        [$tour, $tourDate] = $this->publicTourWithDate();

        $response = $this->postJson('/api/v1/bookings', [
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test',
            'customer_email' => 'test@example.com',
            // no passengers key
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['passengers']);
    }

    /** @test */
    public function it_lists_only_public_tours_by_default(): void
    {
        Tour::factory()->create(['status' => Tour::STATUS_PUBLIC, 'name' => 'Public Tour']);
        Tour::factory()->create(['status' => Tour::STATUS_DRAFT, 'name' => 'Draft Tour']);

        $response = $this->getJson('/api/v1/tours');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Public Tour', $data[0]['name']);
    }

    /** @test */
    public function it_lists_all_tours_when_status_is_all(): void
    {
        Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        Tour::factory()->create(['status' => Tour::STATUS_DRAFT]);

        $response = $this->getJson('/api/v1/tours?status=all');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_updates_booking_status(): void
    {
        [$tour, $tourDate] = $this->publicTourWithDate();
        $booking = Booking::factory()->create([
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'status'         => Booking::STATUS_SUBMITTED,
        ]);

        $response = $this->patchJson("/api/v1/bookings/{$booking->id}", [
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', Booking::STATUS_CONFIRMED);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'Confirmed']);
    }
}
