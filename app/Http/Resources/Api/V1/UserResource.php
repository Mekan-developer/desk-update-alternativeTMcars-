<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'phone'      => $this->phone,
            'name'       => $this->name,
            'gender'     => $this->gender,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'avatar'     => $this->avatar ? Storage::disk('public')->url($this->avatar) : null,
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
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
