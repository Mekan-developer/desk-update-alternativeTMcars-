<?php

namespace App\Services;

use App\Repositories\Interfaces\RegionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RegionService
{
    public function __construct(
        private readonly RegionRepositoryInterface $regions,
    ) {}

    /** Регионы с городами для мобильного приложения: скрытые записи не возвращаются. */
    public function activeList(): Collection
    {
        return $this->regions->activeList();
    }

    /** То же + районы (этрапы) — для каскадных селектов в админке. */
    public function activeListWithDistricts(): Collection
    {
        return $this->regions->activeListWithDistricts();
    }
}
