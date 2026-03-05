<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'status' => $this->status,
            'tour' => new TourResource($this->whenLoaded('tour')),
            'tour_date' => new TourDateResource($this->whenLoaded('tourDate')),
            'passengers' => PassengerResource::collection($this->whenLoaded('passengers')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
