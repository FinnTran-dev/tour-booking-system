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
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getBookings(
            search: $request->query('search'),
            status: $request->query('status'),
        );

        return response()->json([
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'total'        => $bookings->total(),
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'data' => new BookingResource($this->bookingService->getBooking($id))
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());
            return response()->json(['data' => new BookingResource($booking)], 201);
        } catch (BookingException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        try {
            $booking = $this->bookingService->updateBooking($booking, $request->all());
            return response()->json(['data' => new BookingResource($booking)]);
        } catch (BookingException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
