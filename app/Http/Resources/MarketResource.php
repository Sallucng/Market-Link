<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'location'       => $this->location,
            'address'        => $this->address,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'operating_days' => $this->operating_days,
            'open_time'      => $this->open_time,
            'close_time'     => $this->close_time,
            'status'         => $this->status,
            'farmers_count'  => $this->whenCounted('farmers'),
            'farmers'        => $this->whenLoaded('farmers'),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
