<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'business_name'     => $this->business_name,
            'farm_name'         => $this->farm_name,
            'description'       => $this->description,
            'stall_number'      => $this->stall_number,
            'address'           => $this->address,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'operating_days'    => $this->operating_days,
            'pickup_start_time' => $this->pickup_start_time,
            'pickup_end_time'   => $this->pickup_end_time,
            'is_approved'       => (bool) $this->is_approved,
            'approval_status'   => $this->approval_status,
            'rejection_reason'  => $this->rejection_reason,
            'user'              => $this->whenLoaded('user'),
            'markets'           => $this->whenLoaded('markets'),
            'products_count'    => $this->whenCounted('products'),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
