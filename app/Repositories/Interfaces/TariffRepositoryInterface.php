<?php

namespace App\Repositories\Interfaces;

use App\Models\Tariff;
use Illuminate\Database\Eloquent\Collection;

interface TariffRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Tariff;
    public function create(array $data): Tariff;
    public function update(Tariff $tariff, array $data): Tariff;
    public function delete(Tariff $tariff): void;
    public function getDefault(): ?Tariff;
    public function clearDefault(): void;
}
