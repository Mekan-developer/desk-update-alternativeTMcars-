<?php

namespace App\Repositories;

use App\Models\Listing;
use App\Repositories\Interfaces\ListingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListingRepository implements ListingRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Listing::with('user', 'category', 'region', 'city', 'media')
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['category_id'] ?? null, fn($q, $c) => $q->where('category_id', $c))
            ->when($filters['search'] ?? null, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Listing
    {
        return Listing::with('user', 'category', 'region', 'city', 'media', 'rejectionReason')->findOrFail($id);
    }

    public function create(array $data): Listing
    {
        return Listing::create($data);
    }

    public function update(Listing $listing, array $data): Listing
    {
        $listing->update($data);
        return $listing->fresh();
    }

    public function delete(Listing $listing): void
    {
        $listing->delete();
    }

    public function countByStatus(string $status): int
    {
        return Listing::where('status', $status)->count();
    }

    public function countActiveByUser(int $userId): int
    {
        return Listing::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();
    }
}
