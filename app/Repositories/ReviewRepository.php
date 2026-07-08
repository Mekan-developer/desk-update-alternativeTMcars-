<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Review::with('user', 'listing', 'targetUser', 'rejectionReason')
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Review
    {
        return Review::with('user', 'listing', 'targetUser', 'rejectionReason')->findOrFail($id);
    }

    public function create(array $data): Review
    {
        return Review::create($data);
    }

    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->fresh();
    }

    public function countPending(): int
    {
        return Review::where('status', 'pending')->count();
    }
}
