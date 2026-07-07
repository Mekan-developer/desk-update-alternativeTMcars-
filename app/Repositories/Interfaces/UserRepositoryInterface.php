<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function find(int $id): User;
    public function findByPhone(string $phone): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): void;
    public function updateFcmToken(User $user, string $token): void;
    public function updateLocale(User $user, ?string $locale): void;
}
