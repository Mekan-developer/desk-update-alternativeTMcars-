<?php

namespace App\Services;

use App\Models\Tariff;
use App\Models\User;
use App\Repositories\Interfaces\ListingRepositoryInterface;
use App\Repositories\Interfaces\TariffRepositoryInterface;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TariffService
{
    public function __construct(
        private readonly TariffRepositoryInterface $tariffRepository,
        private readonly ListingRepositoryInterface $listingRepository,
        private readonly VideoRepositoryInterface $videoRepository,
    ) {}

    public function all(): Collection
    {
        return $this->tariffRepository->all();
    }

    public function getRemainingLimits(User $user): array
    {
        $tariff = $user->activeTariff();

        if (! $tariff) {
            return ['listings' => 0, 'videos' => 0, 'boosts' => 0];
        }

        $usedListings = $this->listingRepository->countActiveByUser($user->id);
        $usedVideos   = $this->videoRepository->countActiveByUser($user->id);

        return [
            'listings' => max(0, $tariff->listings_limit - $usedListings),
            'videos'   => max(0, $tariff->videos_limit - $usedVideos),
            'boosts'   => $tariff->boost_limit,
        ];
    }

    public function assignToUser(User $user, Tariff $tariff): void
    {
        $user->update([
            'tariff_id'      => $tariff->id,
            'tariff_ends_at' => now()->addDays($tariff->duration_days),
        ]);
    }

    public function store(array $data): Tariff
    {
        if (! empty($data['is_default']) && $data['is_default']) {
            $this->tariffRepository->clearDefault();
        }
        return $this->tariffRepository->create($data);
    }

    public function update(Tariff $tariff, array $data): Tariff
    {
        if (! empty($data['is_default']) && $data['is_default']) {
            $this->tariffRepository->clearDefault();
        }
        return $this->tariffRepository->update($tariff, $data);
    }

    public function delete(Tariff $tariff): void
    {
        $this->tariffRepository->delete($tariff);
    }
}
