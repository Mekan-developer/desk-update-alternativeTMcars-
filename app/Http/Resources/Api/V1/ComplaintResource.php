<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'status'              => $this->status,
            'listing_id'          => $this->listing_id,
            'complaint_reason_id' => $this->complaint_reason_id,
            'text'                => $this->text,
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
