<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function update(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    /** Поднимать можно только своё и уже одобренное объявление */
    public function boost(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id && $listing->status === 'approved';
    }
}
