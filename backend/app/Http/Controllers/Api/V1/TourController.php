<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTourRequest;
use App\Http\Requests\V1\UpdateTourRequest;
use App\Http\Resources\V1\TourResource;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourController extends Controller
{
    protected TourService $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    /**
     * List paginated tours.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $status = $request->input('status');

        $tours = $this->tourService->getTours($perPage, $search, $status);

        return TourResource::collection($tours);
    }

    /**
     * Store a newly created Tour in draft status.
     */
    public function store(StoreTourRequest $request): JsonResponse
    {
        $tour = $this->tourService->createTour($request->validated());

        return response()->json([
            'data' => new TourResource($tour)
        ], 201);
    }

    /**
     * Display the specified Tour.
     */
    public function show(Tour $tour): JsonResponse
    {
        return response()->json([
            'data' => new TourResource($tour->load('tourDates'))
        ]);
    }

    /**
     * Update the specified Tour.
     */
    public function update(UpdateTourRequest $request, Tour $tour): JsonResponse
    {
        $updatedTour = $this->tourService->updateTour($tour, $request->validated());

        return response()->json([
            'data' => new TourResource($updatedTour)
        ], 200);
    }
}
