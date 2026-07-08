<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'text'           => $this->text,
            'rating'         => $this->rating,
            'status'         => $this->status,
            'listing_id'     => $this->listing_id,
            'target_user_id' => $this->target_user_id,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
