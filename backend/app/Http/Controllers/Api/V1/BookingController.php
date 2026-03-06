<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBookingRequest;
use App\Http\Resources\V1\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * List all bookings with relations.
     */
    public function index(): JsonResponse
    {
        $bookings = $this->bookingService->getBookings();
        return response()->json([
            'data' => BookingResource::collection($bookings)
        ]);
    }

    /**
     * Display the specified Booking.
     */
    public function show($id): JsonResponse
    {
        $booking = $this->bookingService->getBooking($id);
        return response()->json([
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Create a new booking with transactional safeguards.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());

            return response()->json([
                'data' => new BookingResource($booking)
            ], 201);
        } catch (BookingException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified Booking.
     */
    public function update(Request $request, Booking $booking): JsonResponse
    {
        try {
            $updated = $this->bookingService->updateBooking($booking, $request->all());
            return response()->json([
                'data' => new BookingResource($updated)
            ]);
        } catch (BookingException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
