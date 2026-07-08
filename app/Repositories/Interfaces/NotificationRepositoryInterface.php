<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    /**
     * @return array<string, \Illuminate\Support\Collection>
     */
    public function pendingItems(int $limitPerCategory = 20): array;

    /** @return array<int, string> */
    public function dismissedKeys(int $userId): array;

    public function dismiss(int $userId, string $key): void;
}
