<?php

namespace App\Actions;

use App\Models\User;
use App\Repositories\Interfaces\VideoRepositoryInterface;

class CheckVideoLimitAction
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository,
    ) {}

    /**
     * Квота тарифа на ролики (ТЗ §7.5): занято = pending + approved
     * (rejected и удалённые место не занимают) — как у объявлений.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 при исчерпании
     */
    public function execute(User $user): void
    {
        $tariff = $user->activeTariff();

        $used = $this->videoRepository->countByUserAndStatuses($user->id, ['pending', 'approved']);

        if (! $tariff || $used >= $tariff->videos_limit) {
            abort(403, __('messages.tariff_limit_exceeded'));
        }
    }
}
