<?php

namespace App\Repositories;

use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VideoRepository implements VideoRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Video::with('user')
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['search'] ?? null, fn($q, $s) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%")))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Video
    {
        return Video::with('user', 'rejectionReason')->findOrFail($id);
    }

    public function update(Video $video, array $data): Video
    {
        $video->update($data);
        return $video->fresh();
    }

    public function delete(Video $video): void
    {
        $video->delete();
    }

    public function countAll(): int
    {
        return Video::count();
    }

    public function countByStatus(string $status): int
    {
        return Video::where('status', $status)->count();
    }

    public function countActiveByUser(int $userId): int
    {
        return Video::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();
    }

    public function sumLikes(): int
    {
        return (int) Video::sum('likes_count');
    }
}
