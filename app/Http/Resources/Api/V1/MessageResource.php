<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'sender'     => $this->sender,
            'text'       => $this->text,
            'is_read'    => (bool) $this->is_read,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
