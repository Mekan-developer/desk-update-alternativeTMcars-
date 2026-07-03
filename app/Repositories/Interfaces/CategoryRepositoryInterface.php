<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function tree(): Collection;
    public function activeTree(): Collection;
    public function find(int $id): Category;
    public function create(array $data): Category;
    public function update(Category $category, array $data): Category;
    public function delete(Category $category): void;
    public function siblings(?int $parentId): Collection;
    public function descendants(Category $category): Collection;
}
