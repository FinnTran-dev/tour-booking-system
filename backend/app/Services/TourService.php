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
     * Get a paginated list of tours.
     * - Default: only Public tours (as required by spec for public-facing endpoint).
     * - Admin can pass ?status=Draft to see drafts, or ?status=all for everything.
     * - Prevents N+1 by eager loading only ENABLED tour dates.
     */
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
                    'status' => TourDate::STATUS_ENABLED,
                ]);

                // Dispatch auto-publish job if the date is in the future
                $parsedDate = \Carbon\Carbon::parse($dateValue)->startOfDay();
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
            $incomingIds = collect($data['dates'])->pluck('id')->filter()->toArray();

            // Optional: Delete dates that are not in the incoming list (Full Sync)
            // $tour->tourDates()->whereNotIn('id', $incomingIds)->delete();

            foreach ($data['dates'] as $dateObj) {
                $id = $dateObj['id'] ?? null;
                $dateValue = $dateObj['date'];
                $endDateValue = $dateObj['end_date'] ?? $dateValue;

                $tour->tourDates()->updateOrCreate(
                    ['id' => $id],
                    [
                        'date' => $dateValue,
                        'end_date' => $endDateValue,
                        'status' => TourDate::STATUS_ENABLED
                    ]
                );

                // Re-schedule Auto Publish job
                $parsedDate = \Carbon\Carbon::parse($dateValue)->startOfDay();
                if ($parsedDate->isFuture()) {
                    PublishTourJob::dispatch($tour, Tour::STATUS_PUBLIC)->delay($parsedDate);
                }
            }
        }

        return $tour->load('tourDates');
    }
}
