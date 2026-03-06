<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourDate;
use App\Jobs\PublishTourJob;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class TourService
{
    /**
     * Get a paginated list of tours, with optional search and status filter.
     * Prevents N+1 by eager loading only ENABLED tour dates.
     */
    public function getTours(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return Tour::query()
            ->when($status, function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when($search, function (Builder $query, $search) {
                // Search by tour name
                $query->where('name', 'like', '%' . $search . '%');
            })
            // Eager load ONLY enabled tour dates to avoid N+1 and filter them
            ->with(['tourDates' => function ($query) {
                $query->where('status', TourDate::STATUS_ENABLED);
            }])
            ->paginate($perPage);
    }

    /**
     * Create a new tour
     */
    public function createTour(array $data): Tour
    {
        // Force the status to Draft upon creation
        $data['status'] = Tour::STATUS_DRAFT;

        $tour = Tour::create($data);

        // Auto-create tour dates if provided
        if (!empty($data['dates'])) {
            foreach ($data['dates'] as $date) {
                $tour->tourDates()->create([
                    'date' => $date,
                    'status' => TourDate::STATUS_ENABLED,
                ]);

                // Dispatch auto-publish job if the date is in the future
                $parsedDate = Carbon::parse($date)->startOfDay();
                if ($parsedDate->isFuture()) {
                    PublishTourJob::dispatch($tour, Tour::STATUS_PUBLIC)->delay($parsedDate);
                }
            }
        }

        return $tour->load('tourDates');
    }

    /**
     * Update an existing tour
     */
    public function updateTour(Tour $tour, array $data): Tour
    {
        $tour->update($data);

        // Synchronize tour dates if provided
        if (isset($data['dates'])) {
            // Very simple replacement strategy: disable old, insert new.
            // Or a more robust logic would be to sync them.
            // For now, let's just add new ones dynamically.
            foreach ($data['dates'] as $date) {
                $tour->tourDates()->updateOrCreate(
                    ['date' => $date],
                    ['status' => TourDate::STATUS_ENABLED]
                );

                // Re-schedule Auto Publish job
                $parsedDate = Carbon::parse($date)->startOfDay();
                if ($parsedDate->isFuture()) {
                    PublishTourJob::dispatch($tour, Tour::STATUS_PUBLIC)->delay($parsedDate);
                }
            }
        }

        return $tour->load('tourDates');
    }
}
