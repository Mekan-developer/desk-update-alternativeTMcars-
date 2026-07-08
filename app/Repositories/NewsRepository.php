<?php

namespace App\Repositories;

use App\Models\News;
use App\Repositories\Interfaces\NewsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsRepository implements NewsRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return News::with('author')
            ->when($filters['type'] ?? null, fn($q, $t) => $q->where('type', $t))
            ->when(
                isset($filters['published']) && $filters['published'] !== '',
                fn($q) => $q->where('is_published', filter_var($filters['published'], FILTER_VALIDATE_BOOLEAN)),
            )
            ->when($filters['search'] ?? null, fn($q, $s) => $q->where('title_ru', 'like', "%$s%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function countByPublished(bool $published): int
    {
        return News::where('is_published', $published)->count();
    }

    public function find(int $id): News
    {
        return News::findOrFail($id);
    }

    public function create(array $data): News
    {
        return News::create($data);
    }

    public function update(News $news, array $data): News
    {
        $news->update($data);
        return $news->fresh();
    }

    public function delete(News $news): void
    {
        $news->delete();
    }
}
