<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Tour;

class PublishTourJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tour;
    public $status;

    /**
     * Create a new job instance.
     */
    public function __construct(Tour $tour, string $status = Tour::STATUS_PUBLIC)
    {
        $this->tour = $tour;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Change status over queue if it's still available according to business logic
        // A condition can be placed here to verify if it really should switch
        $this->tour->update(['status' => $this->status]);
        \Illuminate\Support\Facades\Log::info("Tour ID {$this->tour->id} automatically changed to {$this->status} via queue.");
    }
}
