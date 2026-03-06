<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Console\Command;

class DisableExpiredTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:disable-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable tour dates that have passed and set tour to Draft if all dates are disabled.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay()->toDateString();

        // 1. Disable all tour dates that are in the past
        $disabledCount = TourDate::where('date', '<', $today)
            ->where('status', TourDate::STATUS_ENABLED)
            ->update(['status' => TourDate::STATUS_DISABLED]);

        $this->info("Disabled {$disabledCount} past tour dates.");

        // 2. Find tours that have NO enabled dates and are currently Public
        // and set them to Draft.
        $toursToDraft = Tour::where('status', Tour::STATUS_PUBLIC)
            ->whereDoesntHave('tourDates', function ($query) {
                $query->where('status', TourDate::STATUS_ENABLED);
            })->get();

        foreach ($toursToDraft as $tour) {
            $tour->update(['status' => Tour::STATUS_DRAFT]);
            $this->info("Tour ID {$tour->id} ({$tour->name}) set to Draft (no upcoming dates).");
        }

        $this->info('Completed disabling expired tours and dates.');
    }
}
