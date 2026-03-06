<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Passenger;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookingService
{
    public function getBookings(int $perPage = 20, ?string $search = null, ?string $status = null)
    {
        return Booking::with(['tour', 'tourDate', 'passengers', 'invoice'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where(
                fn($q2) => $q2
                    ->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate($perPage);
    }

    public function getBooking(int $id): Booking
    {
        return Booking::with(['tour', 'tourDate', 'passengers', 'invoice'])->findOrFail($id);
    }

    /**
     * @throws BookingException
     */
    public function createBooking(array $data): Booking
    {
        $tour = Tour::findOrFail($data['tour_id']);
        if ($tour->status !== Tour::STATUS_PUBLIC) {
            throw new BookingException('Cannot book a tour that is not Public.');
        }

        $tourDate = TourDate::findOrFail($data['tour_date_id']);
        if ($tourDate->tour_id !== $tour->id) {
            throw new BookingException('Tour date does not belong to the selected tour.');
        }
        if ($tourDate->status !== TourDate::STATUS_ENABLED) {
            throw new BookingException('Cannot book an inactive tour date.');
        }

        if (empty($data['passengers'])) {
            throw new BookingException('A booking must contain at least one passenger.');
        }

        try {
            return DB::transaction(function () use ($data) {
                $booking = Booking::create([
                    'tour_id'        => $data['tour_id'],
                    'tour_date_id'   => $data['tour_date_id'],
                    'customer_name'  => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                    'status'         => Booking::STATUS_SUBMITTED,
                ]);

                $pivotData = [];
                foreach ($data['passengers'] as $passengerData) {
                    if (isset($passengerData['id'])) {
                        $pivotData[$passengerData['id']] = ['special_request' => $passengerData['special_request'] ?? null];
                        continue;
                    }

                    $passenger = Passenger::create(array_merge($passengerData, ['status' => Passenger::STATUS_ENABLED]));
                    $pivotData[$passenger->id] = ['special_request' => $passengerData['special_request'] ?? null];
                }

                $booking->passengers()->attach($pivotData);

                $booking->invoice()->create([
                    'amount' => count($pivotData) * 100,
                    'status' => Invoice::STATUS_UNPAID,
                ]);

                return $booking->load(['tour', 'tourDate', 'passengers', 'invoice']);
            });
        } catch (Throwable $e) {
            if ($e instanceof BookingException) {
                throw $e;
            }
            throw new BookingException('Booking failed: ' . $e->getMessage());
        }
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->update([
                'tour_date_id'   => $data['tour_date_id'] ?? $booking->tour_date_id,
                'customer_name'  => $data['customer_name'] ?? $booking->customer_name,
                'customer_email' => $data['customer_email'] ?? $booking->customer_email,
                'status'         => $data['status'] ?? $booking->status,
            ]);

            if (isset($data['passengers'])) {
                $pivotData = collect($data['passengers'])
                    ->filter(fn($p) => isset($p['id']))
                    ->keyBy('id')
                    ->map(fn($p) => ['special_request' => $p['special_request'] ?? null])
                    ->toArray();

                $booking->passengers()->sync($pivotData);

                $booking->invoice?->update(['amount' => count($pivotData) * 100]);
            }

            return $booking->fresh(['tour', 'tourDate', 'passengers', 'invoice']);
        });
    }
}
