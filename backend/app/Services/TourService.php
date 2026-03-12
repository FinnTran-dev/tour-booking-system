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
    public function getTours(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return Tour::query()
            ->when($status === 'all', fn($q) => $q) // No filter — show all (admin)
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when(!$status, fn($q) => $q->where('status', Tour::STATUS_PUBLIC)) // Default: Public only
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
            foreach ($data['dates'] as $dateObj) {
                $dateValue = is_array($dateObj) ? $dateObj['date'] : $dateObj;
                $endDateValue = is_array($dateObj) ? ($dateObj['end_date'] ?? $dateValue) : $dateValue;

                $tour->tourDates()->create([
                    'date' => $dateValue,
                    'end_date' => $endDateValue,
                    'capacity' => $dateObj['capacity'] ?? 10,
                    'status' => $dateObj['status'] ?? TourDate::STATUS_ENABLED,
                ]);
            }
        }

        return $tour->load('tourDates');
    }

    /**
     * Update an existing tour
     */
    public function updateTour(Tour $tour, array $data): Tour
    {
        // Global check for stale data (Optimistic Locking)
        if (isset($data['last_updated_at'])) {
            $clientTimestamp = Carbon::parse($data['last_updated_at'])->toDateTimeString();
            $dbTimestamp = $tour->updated_at->toDateTimeString();

            if ($clientTimestamp !== $dbTimestamp) {
                throw new \App\Exceptions\BookingException("This tour has been modified by another user. Please reload the page to get the latest data.");
            }
        }

        $tour->update($data);

        // Synchronize tour dates if provided
        if (isset($data['dates'])) {
            $incomingIds = collect($data['dates'])->pluck('id')->filter()->toArray();

            foreach ($data['dates'] as $dateObj) {
                $id = $dateObj['id'] ?? null;
                $dateValue = $dateObj['date'];
                $endDateValue = $dateObj['end_date'] ?? $dateValue;

                $tour->tourDates()->updateOrCreate(
                    ['id' => $id],
                    [
                        'date' => $dateValue,
                        'end_date' => $endDateValue,
                        'capacity' => $dateObj['capacity'] ?? 10,
                        'status' => $dateObj['status'] ?? TourDate::STATUS_ENABLED
                    ]
                );
            }
        }

        return $tour->load('tourDates');
    }
}
