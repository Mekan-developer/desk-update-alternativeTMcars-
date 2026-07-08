<?php

namespace App\Repositories;

use App\Models\Banner;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BannerRepository implements BannerRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Banner::query()
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title_ru', 'like', "%$s%"))
            ->when(($filters['active'] ?? null) !== null, fn ($q) => $q->where('is_active', (bool) $filters['active']))
            ->orderBy('sort_order')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function ordered(): Collection
    {
        return Banner::orderBy('sort_order')->get();
    }

    public function find(int $id): Banner
    {
        return Banner::findOrFail($id);
    }

    public function create(array $data): Banner
    {
        $data['sort_order'] ??= (int) Banner::max('sort_order') + 1;

        return Banner::create($data);
    }

    public function update(Banner $banner, array $data): Banner
    {
        $banner->update($data);

        return $banner->fresh();
    }

    public function delete(Banner $banner): void
    {
        $banner->delete();
    }

    public function activeForApi(): Collection
    {
        $now = now();

        return Banner::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')
            ->get();
    }
}
