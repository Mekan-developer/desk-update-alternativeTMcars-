<?php

namespace App\Actions;

use App\Models\User;
use App\Services\UserService;

class BlockUserAction
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function execute(User $user, ?string $reason = null): void
    {
        $this->userService->block($user, $reason);
    }
}
