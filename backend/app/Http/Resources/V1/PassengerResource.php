<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'given_name' => $this->given_name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth->format('Y-m-d'),
            'status' => $this->status,
            'special_request' => $this->whenPivotLoaded('booking_passenger', function () {
                return $this->pivot->special_request;
            }),
        ];
    }
}
