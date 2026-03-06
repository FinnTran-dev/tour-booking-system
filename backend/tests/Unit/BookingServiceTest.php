<?php

namespace Tests\Unit;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Passenger;
use App\Models\Tour;
use App\Models\TourDate;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService();
    }

    /** @test */
    public function it_throws_exception_when_booking_a_draft_tour(): void
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_DRAFT]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour->id, 'status' => TourDate::STATUS_ENABLED]);

        $this->expectException(BookingException::class);
        $this->expectExceptionMessage('Cannot book a tour that is not Public.');

        $this->service->createBooking([
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test User',
            'customer_email' => 'test@example.com',
            'passengers'     => [
                ['given_name' => 'John', 'surname' => 'Doe', 'date_of_birth' => '1990-01-01'],
            ],
        ]);
    }

    /** @test */
    public function it_throws_exception_when_booking_with_disabled_date(): void
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour->id, 'status' => TourDate::STATUS_DISABLED]);

        $this->expectException(BookingException::class);
        $this->expectExceptionMessage('Cannot book an inactive tour date.');

        $this->service->createBooking([
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test User',
            'customer_email' => 'test@example.com',
            'passengers'     => [
                ['given_name' => 'John', 'surname' => 'Doe', 'date_of_birth' => '1990-01-01'],
            ],
        ]);
    }

    /** @test */
    public function it_throws_exception_when_booking_with_no_passengers(): void
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour->id, 'status' => TourDate::STATUS_ENABLED]);

        $this->expectException(BookingException::class);
        $this->expectExceptionMessage('A booking must contain at least one passenger.');

        $this->service->createBooking([
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test User',
            'customer_email' => 'test@example.com',
            'passengers'     => [],
        ]);
    }

    /** @test */
    public function it_creates_booking_atomically_with_passengers_and_invoice(): void
    {
        $tour = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour->id, 'status' => TourDate::STATUS_ENABLED]);

        $booking = $this->service->createBooking([
            'tour_id'        => $tour->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'passengers'     => [
                ['given_name' => 'Jane', 'surname' => 'Doe', 'date_of_birth' => '1985-05-15'],
                ['given_name' => 'John', 'surname' => 'Doe', 'date_of_birth' => '1987-03-20'],
            ],
        ]);

        // Booking exists with correct status
        $this->assertDatabaseHas('bookings', [
            'id'     => $booking->id,
            'status' => Booking::STATUS_SUBMITTED,
        ]);

        // Passengers were created and attached
        $this->assertCount(2, $booking->passengers);
        $this->assertDatabaseCount('passengers', 2);

        // Invoice auto-created as Unpaid with correct amount ($100 × 2 pax)
        $this->assertNotNull($booking->invoice);
        $this->assertEquals(Invoice::STATUS_UNPAID, $booking->invoice->status);
        $this->assertEquals(200.00, (float) $booking->invoice->amount);
    }

    /** @test */
    public function it_throws_exception_when_tour_date_does_not_belong_to_tour(): void
    {
        $tour1 = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tour2 = Tour::factory()->create(['status' => Tour::STATUS_PUBLIC]);
        $tourDate = TourDate::factory()->create(['tour_id' => $tour2->id, 'status' => TourDate::STATUS_ENABLED]);

        $this->expectException(BookingException::class);
        $this->expectExceptionMessage('Tour date does not belong to the selected tour.');

        $this->service->createBooking([
            'tour_id'        => $tour1->id,
            'tour_date_id'   => $tourDate->id,
            'customer_name'  => 'Test User',
            'customer_email' => 'test@example.com',
            'passengers'     => [
                ['given_name' => 'John', 'surname' => 'Doe', 'date_of_birth' => '1990-01-01'],
            ],
        ]);
    }
}
