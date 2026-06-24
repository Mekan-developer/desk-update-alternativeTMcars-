<?php

namespace App\Observers;

use App\Models\Listing;

class ListingObserver
{
    public function creating(Listing $listing): void
    {
        if (empty($listing->status)) {
            $listing->status = 'pending';
        }
    }
}
