<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ru' => $this->title_ru,
            'title_tk' => $this->title_tk,
            'content_ru' => $this->content_ru,
            'content_tk' => $this->content_tk,
            'image' => $this->image ? '/storage/'.$this->image : null,
            'type' => $this->type,
            'ad_link_type' => $this->ad_link_type,
            'ad_link_id' => $this->ad_link_id,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
