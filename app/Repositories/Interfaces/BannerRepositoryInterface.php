<?php

namespace App\Repositories\Interfaces;

use App\Models\Banner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BannerRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function ordered(): Collection;
    public function find(int $id): Banner;
    public function create(array $data): Banner;
    public function update(Banner $banner, array $data): Banner;
    public function delete(Banner $banner): void;
    public function activeForApi(): Collection;
}
