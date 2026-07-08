<?php

namespace App\Repositories;

use App\Models\Favorite;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function add(int $userId, int $listingId): Favorite
    {
        return Favorite::firstOrCreate([
            'user_id'    => $userId,
            'listing_id' => $listingId,
        ]);
    }

    public function remove(int $userId, int $listingId): void
    {
        Favorite::where('user_id', $userId)->where('listing_id', $listingId)->delete();
    }

    public function paginateForUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Favorite::with('listing.media', 'listing.category')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
