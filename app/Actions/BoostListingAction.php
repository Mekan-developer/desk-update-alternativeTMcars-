<?php

namespace App\Actions;

use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Validation\ValidationException;

class BoostListingAction
{
    public function __construct(
        private readonly ListingService $listingService,
    ) {}

    public function execute(Listing $listing): void
    {
        if (! $this->listingService->canBoost($listing)) {
            throw ValidationException::withMessages([
                'listing' => [__('messages.boost_interval_not_passed')],
            ]);
        }

        $this->listingService->boost($listing);
    }
}
