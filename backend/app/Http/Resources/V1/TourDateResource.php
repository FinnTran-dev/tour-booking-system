<?php

namespace App\Http\Resources\V1;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourDateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : $this->date->format('Y-m-d'),
            'capacity' => $this->capacity,
            'booked_count' => $this->bookings()->whereIn('status', [Booking::STATUS_SUBMITTED, Booking::STATUS_CONFIRMED])
                ->join('booking_passenger', 'bookings.id', '=', 'booking_passenger.booking_id')
                ->count(),
            'status' => $this->status,
        ];
    }
}
