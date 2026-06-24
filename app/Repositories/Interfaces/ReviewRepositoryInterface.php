<?php

namespace App\Repositories\Interfaces;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function find(int $id): Review;
    public function update(Review $review, array $data): Review;
    public function countPending(): int;
}
