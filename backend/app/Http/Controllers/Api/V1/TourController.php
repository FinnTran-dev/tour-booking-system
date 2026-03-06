<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTourRequest;
use App\Http\Requests\V1\UpdateTourRequest;
use App\Http\Resources\V1\BookingResource;
use App\Http\Resources\V1\TourResource;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourController extends Controller
{
    public function __construct(protected TourService $tourService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $tours = $this->tourService->getTours(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            status: $request->input('status'),
        );

        return TourResource::collection($tours);
    }

    public function store(StoreTourRequest $request): JsonResponse
    {
        $tour = $this->tourService->createTour($request->validated());

        return response()->json(['data' => new TourResource($tour)], 201);
    }

    public function show(Tour $tour): JsonResponse
    {
        return response()->json(['data' => new TourResource($tour->load('tourDates'))]);
    }

    public function update(UpdateTourRequest $request, Tour $tour): JsonResponse
    {
        $tour = $this->tourService->updateTour($tour, $request->validated());

        return response()->json(['data' => new TourResource($tour)]);
    }

    public function bookings(Request $request, Tour $tour): JsonResponse
    {
        $bookings = $tour->bookings()
            ->with(['tourDate', 'passengers', 'invoice'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return response()->json(['data' => BookingResource::collection($bookings)]);
    }
}
