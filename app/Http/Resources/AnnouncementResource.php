<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'admin_id'        => $this->admin_id,
            'title'           => $this->title,
            'message'         => $this->message,
            'target_role'     => $this->target_role,
            'target_audience' => $this->target_role,
            'is_active'       => (bool) $this->is_active,
            'expires_at'      => $this->expires_at?->toISOString(),
            'admin'           => $this->whenLoaded('admin'),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
