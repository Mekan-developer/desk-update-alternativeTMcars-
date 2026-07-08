<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

class ReviewService
{
    public function __construct(
        private readonly ReviewRepositoryInterface $reviewRepository,
    ) {}

    public function createFromApi(User $user, array $data): Review
    {
        return $this->reviewRepository->create([
            'user_id'             => $user->id,
            'listing_id'          => $data['listing_id'] ?? null,
            'target_user_id'      => $data['target_user_id'] ?? null,
            'text'                => $data['text'],
            'rating'              => $data['rating'] ?? null,
            'status'              => 'pending',
            'rejection_reason_id' => null,
        ]);
    }
}
