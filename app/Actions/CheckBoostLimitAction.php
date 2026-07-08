<?php

namespace App\Actions;

use App\Models\Listing;
use App\Repositories\Interfaces\ListingRepositoryInterface;

class CheckBoostLimitAction
{
    public function __construct(
        private readonly ListingRepositoryInterface $listingRepository,
    ) {}

    /**
     * Квота тарифа на поднятия: сколько объявлений пользователя может быть
     * одновременно в статусе «поднято» (ТЗ 6.2 «количество поднятий»).
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 при исчерпании
     */
    public function execute(Listing $listing): void
    {
        // Повторное поднятие уже поднятого объявления слот не занимает —
        // это лишь обновление позиции по истечении интервала.
        if ($listing->is_boosted) {
            return;
        }

        $tariff = $listing->user->activeTariff();

        $used = $this->listingRepository->countBoostedByUser($listing->user_id);

        if (! $tariff || $used >= $tariff->boost_limit) {
            abort(403, __('messages.boost_limit_exceeded'));
        }
    }
}
