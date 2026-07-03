<?php

namespace App\Repositories;

use App\Models\CategoryIcon;
use App\Repositories\Interfaces\CategoryIconRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryIconRepository implements CategoryIconRepositoryInterface
{
    public function all(): Collection
    {
        return CategoryIcon::orderBy('is_system', 'desc')->orderBy('id')->get();
    }

    public function create(array $data): CategoryIcon
    {
        return CategoryIcon::create($data);
    }

    public function existsForPath(string $path): bool
    {
        return CategoryIcon::where('path', $path)->exists();
    }
}
