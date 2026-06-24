<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters);
    }

    public function store(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function delete(User $user): void
    {
        $this->userRepository->delete($user);
    }

    public function block(User $user, ?string $reason): void
    {
        $this->userRepository->update($user, [
            'status'         => 'blocked',
            'blocked_reason' => $reason,
            'blocked_at'     => now(),
        ]);
    }

    public function unblock(User $user): void
    {
        $this->userRepository->update($user, [
            'status'         => 'active',
            'blocked_reason' => null,
            'blocked_at'     => null,
        ]);
    }
}
