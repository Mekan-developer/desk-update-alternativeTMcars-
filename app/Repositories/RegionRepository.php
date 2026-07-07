<?php

namespace App\Repositories;

use App\Models\Region;
use App\Repositories\Interfaces\RegionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository implements RegionRepositoryInterface
{
    public function activeList(): Collection
    {
        return Region::with(['cities' => fn ($q) => $q->where('is_hidden', false)->orderBy('name_ru')])
            ->where('is_hidden', false)
            ->orderBy('name_ru')
            ->get();
    }

    public function activeListWithDistricts(): Collection
    {
        return Region::with([
                'cities' => fn ($q) => $q->where('is_hidden', false)->orderBy('name_ru'),
                'cities.districts' => fn ($q) => $q->where('is_hidden', false)->orderBy('name_ru'),
            ])
            ->where('is_hidden', false)
            ->orderBy('name_ru')
            ->get();
    }
}
