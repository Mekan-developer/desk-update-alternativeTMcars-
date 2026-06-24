<?php

namespace App\Actions;

use App\Models\Tariff;
use App\Models\User;
use App\Services\TariffService;

class AssignTariffAction
{
    public function __construct(
        private readonly TariffService $tariffService,
    ) {}

    public function execute(User $user, Tariff $tariff): void
    {
        $this->tariffService->assignToUser($user, $tariff);
    }
}
