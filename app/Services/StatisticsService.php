<?php

namespace App\Services;

use App\Repositories\Interfaces\ListingRepositoryInterface;
use App\Repositories\Interfaces\TariffRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class StatisticsService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ListingRepositoryInterface $listingRepository,
        private readonly VideoRepositoryInterface $videoRepository,
        private readonly TariffRepositoryInterface $tariffRepository,
    ) {}

    /**
     * Границы периода: произвольный диапазон from/to имеет приоритет,
     * иначе пресет day (сегодня) | week (7 дней) | month (30 дней).
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public function resolvePeriod(?string $period, ?string $from, ?string $to): array
    {
        if ($from || $to) {
            return [
                $from ? Carbon::parse($from)->startOfDay() : now()->startOfDay(),
                $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay(),
            ];
        }

        return match ($period) {
            'week'  => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'month' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    public function getOverview(CarbonInterface $from, CarbonInterface $to): array
    {
        $totalUsers = $this->userRepository->countUsers();

        return [
            'stats' => [
                'users' => [
                    'total'   => $totalUsers,
                    'period'  => $this->userRepository->countRegisteredBetween($from, $to),
                    'blocked' => $this->userRepository->countBlocked(),
                ],
                'listings' => [
                    'total'    => $this->listingRepository->countAll(),
                    'period'   => $this->listingRepository->countCreatedBetween($from, $to),
                    'pending'  => $this->listingRepository->countByStatus('pending'),
                    'approved' => $this->listingRepository->countByStatus('approved'),
                    'rejected' => $this->listingRepository->countByStatus('rejected'),
                ],
                'videos' => [
                    'total'    => $this->videoRepository->countAll(),
                    'approved' => $this->videoRepository->countByStatus('approved'),
                    'likes'    => $this->videoRepository->sumLikes(),
                ],
            ],
            'tariffStats' => $this->tariffRepository->all()->map(fn($tariff) => [
                'name'  => $tariff->name,
                'count' => $tariff->users_count,
                'pct'   => $totalUsers > 0 ? round($tariff->users_count / $totalUsers * 100) : 0,
            ])->values(),
        ];
    }
}
