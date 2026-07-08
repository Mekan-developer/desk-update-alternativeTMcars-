<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'type'        => $this->type,
            'price'       => $this->price !== null ? (float) $this->price : null,
            'phone'       => $this->phone,
            'tags'        => $this->tags ?? [],
            'location'    => $this->location,
            'status'      => $this->status,
            'rejection_reason' => $this->when(
                $this->status === 'rejected' && $this->relationLoaded('rejectionReason') && $this->rejectionReason,
                fn () => [
                    'id'      => $this->rejectionReason->id,
                    'name_tk' => $this->rejectionReason->name_tk,
                    'name_ru' => $this->rejectionReason->name_ru,
                ]
            ),
            'views'      => $this->views,
            'is_boosted' => (bool) $this->is_boosted,
            'boosted_at' => $this->boosted_at,
            'category'   => $this->whenLoaded('category', fn () => [
                'id'        => $this->category->id,
                'parent_id' => $this->category->parent_id,
                'name_tk'   => $this->category->name_tk,
                'name_ru'   => $this->category->name_ru,
                // Путь от корня до выбранной подкатегории, напр. Оптом → Продукты питания → test
                'path'      => $this->categoryPath(),
            ]),
            'region'     => $this->whenLoaded('region', fn () => [
                'id'      => $this->region->id,
                'name_tk' => $this->region->name_tk,
                'name_ru' => $this->region->name_ru,
            ]),
            'city'       => $this->whenLoaded('city', fn () => [
                'id'      => $this->city->id,
                'name_tk' => $this->city->name_tk,
                'name_ru' => $this->city->name_ru,
            ]),
            'user'       => $this->whenLoaded('user', fn () => [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar ? Storage::disk('public')->url($this->user->avatar) : null,
            ]),
            // Конвертация в WebP идёт в фоновой очереди `media` (обычно 1-3 сек):
            // пока processing=true, все три ссылки указывают на загруженный оригинал
            'photos'     => $this->whenLoaded('media', fn () => $this->media->map(fn ($m) => [
                'id'         => $m->id,
                'order'      => $m->order,
                'processing' => $m->medium_path === null,
                'original'   => Storage::disk('public')->url($m->path),
                'medium'     => Storage::disk('public')->url($m->medium_path ?? $m->path),
                'thumb'      => Storage::disk('public')->url($m->thumb_path ?? $m->path),
            ])->values()),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /** Цепочка категорий от корня до листа (макс. 3 уровня — Category::MAX_LEVEL) */
    private function categoryPath(): array
    {
        $chain = [];
        $category = $this->category;

        while ($category) {
            $chain[] = [
                'id'      => $category->id,
                'name_tk' => $category->name_tk,
                'name_ru' => $category->name_ru,
            ];
            $category = $category->parent;
        }

        return array_reverse($chain);
    }
}
