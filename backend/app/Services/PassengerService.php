<?php

namespace App\Services;

use App\Models\Passenger;
use Illuminate\Pagination\LengthAwarePaginator;

class PassengerService
{
    /**
     * Get a paginated list of passengers
     */
    public function getPassengers(int $perPage = 15): LengthAwarePaginator
    {
        return Passenger::query()
            ->paginate($perPage);
    }

    /**
     * Create a new passenger
     */
    public function createPassenger(array $data): Passenger
    {
        // Force status to Enabled on creation
        $data['status'] = Passenger::STATUS_ENABLED;
        return Passenger::create($data);
    }

    /**
     * Update an existing passenger
     */
    public function updatePassenger(Passenger $passenger, array $data): Passenger
    {
        $passenger->update($data);
        return $passenger;
    }
}
