<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allWithChildren(): Collection
    {
        return Category::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
    }

    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name_ru']);
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function reorder(array $items): void
    {
        foreach ($items as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }
    }
}
