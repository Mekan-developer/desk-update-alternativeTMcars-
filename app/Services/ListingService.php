<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Repositories\Interfaces\ListingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ListingService
{
    public function __construct(
        private readonly ListingRepositoryInterface $listingRepository,
        private readonly TariffService $tariffService,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->listingRepository->paginate($filters);
    }

    public function counts(): array
    {
        return [
            'pending'  => $this->listingRepository->countByStatus('pending'),
            'approved' => $this->listingRepository->countByStatus('approved'),
            'rejected' => $this->listingRepository->countByStatus('rejected'),
        ];
    }

    public function approve(Listing $listing): void
    {
        $this->listingRepository->update($listing, [
            'status'              => 'approved',
            'rejection_reason_id' => null,
        ]);
    }

    public function reject(Listing $listing, int $rejectionReasonId): void
    {
        $this->listingRepository->update($listing, [
            'status'              => 'rejected',
            'rejection_reason_id' => $rejectionReasonId,
        ]);
    }

    public function canBoost(Listing $listing): bool
    {
        if (! $listing->boosted_at) {
            return true;
        }

        $intervalHours = (int) config('settings.boost_interval_hours', 24);

        return $listing->boosted_at->addHours($intervalHours)->isPast();
    }

    public function boost(Listing $listing): void
    {
        $this->listingRepository->update($listing, [
            'is_boosted' => true,
            'boosted_at' => now(),
        ]);
    }

    public function delete(Listing $listing): void
    {
        $this->listingRepository->delete($listing);
    }
}
