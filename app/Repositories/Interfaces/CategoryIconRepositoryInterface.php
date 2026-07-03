<?php

namespace App\Repositories\Interfaces;

use App\Models\CategoryIcon;
use Illuminate\Database\Eloquent\Collection;

interface CategoryIconRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): CategoryIcon;
    public function existsForPath(string $path): bool;
}
