<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PassengerResource;
use App\Models\Passenger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    /**
     * Display a listing of passengers with optional search.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $passengers = Passenger::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->when(
                $search,
                fn($q) =>
                $q->where('given_name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => PassengerResource::collection($passengers),
            'meta' => [
                'current_page' => $passengers->currentPage(),
                'last_page'    => $passengers->lastPage(),
                'total'        => $passengers->total(),
            ],
        ]);
    }

    /**
     * Display the specified passenger.
     */
    public function show(Passenger $passenger): JsonResponse
    {
        return response()->json([
            'data' => new PassengerResource($passenger->load('bookings'))
        ]);
    }
}
