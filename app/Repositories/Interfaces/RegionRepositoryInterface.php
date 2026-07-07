<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface RegionRepositoryInterface
{
    public function activeList(): Collection;
    public function activeListWithDistricts(): Collection;
}
