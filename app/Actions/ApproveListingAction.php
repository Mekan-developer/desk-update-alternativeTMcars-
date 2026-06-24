<?php

namespace App\Actions;

use App\Events\ListingApproved;
use App\Models\Listing;
use App\Services\ListingService;

class ApproveListingAction
{
    public function __construct(
        private readonly ListingService $listingService,
    ) {}

    public function execute(Listing $listing): void
    {
        $this->listingService->approve($listing);

        event(new ListingApproved($listing));
    }
}
