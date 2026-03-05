<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBookingRequest;
use App\Http\Resources\V1\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
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
            // Service level validation exceptions
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
