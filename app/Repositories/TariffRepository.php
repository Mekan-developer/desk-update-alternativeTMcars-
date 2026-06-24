<?php

namespace App\Repositories;

use App\Models\Tariff;
use App\Repositories\Interfaces\TariffRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TariffRepository implements TariffRepositoryInterface
{
    public function all(): Collection
    {
        return Tariff::withCount('users')->orderBy('id')->get();
    }

    public function find(int $id): Tariff
    {
        return Tariff::findOrFail($id);
    }

    public function create(array $data): Tariff
    {
        return Tariff::create($data);
    }

    public function update(Tariff $tariff, array $data): Tariff
    {
        $tariff->update($data);
        return $tariff->fresh();
    }

    public function delete(Tariff $tariff): void
    {
        $tariff->delete();
    }

    public function getDefault(): ?Tariff
    {
        return Tariff::where('is_default', true)->first();
    }

    public function clearDefault(): void
    {
        Tariff::where('is_default', true)->update(['is_default' => false]);
    }
}
