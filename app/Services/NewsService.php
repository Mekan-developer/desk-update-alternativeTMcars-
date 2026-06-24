<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use App\Repositories\Interfaces\NewsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->newsRepository->paginate($filters);
    }

    public function store(array $data, User $author, ?UploadedFile $image = null): News
    {
        if ($image) {
            $data['image'] = $image->store('news', 'public');
        }
        $data['author_id'] = $author->id;

        return $this->newsRepository->create($data);
    }

    public function update(News $news, array $data, ?UploadedFile $image = null): News
    {
        if ($image) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $image->store('news', 'public');
        }

        return $this->newsRepository->update($news, $data);
    }

    public function publish(News $news): void
    {
        $this->newsRepository->update($news, [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(News $news): void
    {
        $this->newsRepository->update($news, [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function delete(News $news): void
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $this->newsRepository->delete($news);
    }
}
