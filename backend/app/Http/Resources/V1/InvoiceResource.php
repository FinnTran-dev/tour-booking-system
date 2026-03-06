<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'amount'     => $this->amount,
            'status'     => $this->status,
            'created_at' => $this->created_at,
            'booking'    => $this->whenLoaded('booking', function () {
                $booking = $this->booking;
                return [
                    'id'             => $booking->id,
                    'customer_name'  => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'status'         => $booking->status,
                    'tour' => $booking->relationLoaded('tour') && $booking->tour ? [
                        'id'   => $booking->tour->id,
                        'name' => $booking->tour->name,
                    ] : null,
                    'tour_date' => $booking->relationLoaded('tourDate') && $booking->tourDate ? [
                        'id'   => $booking->tourDate->id,
                        'date' => $booking->tourDate->date,
                    ] : null,
                ];
            }),
        ];
    }
}
