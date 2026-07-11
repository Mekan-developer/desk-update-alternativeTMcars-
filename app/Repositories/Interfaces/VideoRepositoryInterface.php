<?php

namespace App\Repositories\Interfaces;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VideoRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function find(int $id): Video;
    public function update(Video $video, array $data): Video;
    public function delete(Video $video): void;
    public function countAll(): int;
    public function countByStatus(string $status): int;
    public function countActiveByUser(int $userId): int;
    public function sumLikes(): int;
}
