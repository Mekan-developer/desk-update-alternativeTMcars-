<?php

namespace App\Actions;

use App\Events\ListingRejected;
use App\Models\Listing;
use App\Services\ListingService;

class RejectListingAction
{
    public function __construct(
        private readonly ListingService $listingService,
    ) {}

    public function execute(Listing $listing, int $rejectionReasonId): void
    {
        $this->listingService->reject($listing, $rejectionReasonId);

        event(new ListingRejected($listing));
    }
}
