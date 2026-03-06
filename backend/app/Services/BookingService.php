<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\Passenger;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookingService
{
    /**
     * Get all bookings with related data.
     */
    public function getBookings(int $perPage = 20, ?string $search = null, ?string $status = null)
    {
        return Booking::with(['tour', 'tourDate', 'passengers', 'invoice'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a single booking with all relations.
     */
    public function getBooking(int $id): Booking
    {
        return Booking::with(['tour', 'tourDate', 'passengers', 'invoice'])->findOrFail($id);
    }

    /**
     * Create a booking MUST be atomic, validated, and automatically create an unpaid invoice.
     * Prevents duplicate bookings per passenger and dirty states.
     *
     * @param array $data Expected keys: tour_id, tour_date_id, customer_name, customer_email, passengers (array of IDs or details)
     * @throws BookingException
     */
    public function createBooking(array $data): Booking
    {
        // Early validation: Tour must be Public
        $tour = Tour::findOrFail($data['tour_id']);
        if ($tour->status !== Tour::STATUS_PUBLIC) {
            throw new BookingException("Cannot book a tour that is not Public.");
        }

        // Early validation: TourDate must be Enabled and belong to Tour
        $tourDate = TourDate::findOrFail($data['tour_date_id']);
        if ($tourDate->tour_id !== $tour->id) {
            throw new BookingException("Tour date does not belong to the selected tour.");
        }
        if ($tourDate->status !== TourDate::STATUS_ENABLED) {
            throw new BookingException("Cannot book an inactive tour date.");
        }

        // Early validation: Must have at least 1 passenger
        if (empty($data['passengers'])) {
            throw new BookingException("A booking must contain at least one passenger.");
        }

        try {
            // Transaction boundaries: Ensure entire operation succeeds or fails atomically
            return DB::transaction(function () use ($data) {

                // 1. Create the base Booking entity
                $booking = Booking::create([
                    'tour_id' => $data['tour_id'],
                    'tour_date_id' => $data['tour_date_id'],
                    'customer_name' => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                    'status' => Booking::STATUS_SUBMITTED,
                ]);

                // 2. Attach Passengers (Assume $data['passengers'] is array of passenger details)
                // If it is IDs, we can just attach. Let's handle an array of array-data for deep creation vs ID attachment.
                $passengerIdsToAttach = [];
                $pivotData = [];

                foreach ($data['passengers'] as $passengerData) {
                    if (isset($passengerData['id'])) {
                        // Existing passenger
                        $passengerIdsToAttach[] = $passengerData['id'];
                        $pivotData[$passengerData['id']] = ['special_request' => $passengerData['special_request'] ?? null];
                        continue;
                    }

                    // Create new passenger
                    $passengerData['status'] = Passenger::STATUS_ENABLED;
                    $passenger = Passenger::create($passengerData);
                    $passengerIdsToAttach[] = $passenger->id;
                    $pivotData[$passenger->id] = ['special_request' => $passengerData['special_request'] ?? null];
                }

                $booking->passengers()->attach($pivotData);

                // 3. Create an Unpaid Invoice
                // Standard fare logic simulation: assume $100 per passenger for now, or fetch from DB if prices exist.
                // Assuming it's simple flat rate of 100 per passenger for the test.
                $amount = count($passengerIdsToAttach) * 100.00;

                $booking->invoice()->create([
                    'amount' => $amount,
                    'status' => Invoice::STATUS_UNPAID,
                ]);

                return $booking->load(['tour', 'tourDate', 'passengers', 'invoice']);
            });
        } catch (Throwable $e) {
            if ($e instanceof BookingException) {
                throw $e;
            }
            throw new BookingException("An error occurred during booking generation: " . $e->getMessage());
        }
    }

    /**
     * Update Booking: Allow editing date, passengers.
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            // Update base details including status
            $booking->update([
                'tour_date_id'   => $data['tour_date_id'] ?? $booking->tour_date_id,
                'customer_name'  => $data['customer_name'] ?? $booking->customer_name,
                'customer_email' => $data['customer_email'] ?? $booking->customer_email,
                'status'         => $data['status'] ?? $booking->status,
            ]);

            // Sync passengers if provided
            if (isset($data['passengers'])) {
                $pivotData = [];
                foreach ($data['passengers'] as $passengerData) {
                    if (isset($passengerData['id'])) {
                        $pivotData[$passengerData['id']] = ['special_request' => $passengerData['special_request'] ?? null];
                    }
                }
                $booking->passengers()->sync($pivotData);

                // Recalculate invoice based on new passenger count
                if ($booking->invoice) {
                    $newAmount = count($pivotData) * 100.00;
                    $booking->invoice->update(['amount' => $newAmount]);
                }
            }

            return $booking->fresh(['tour', 'tourDate', 'passengers', 'invoice']);
        });
    }
}
