<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use App\Repositories\Interfaces\ComplaintRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ComplaintService
{
    public function __construct(
        private readonly ComplaintRepositoryInterface $complaintRepository,
    ) {}

    public function activeReasons(): Collection
    {
        return $this->complaintRepository->activeReasons();
    }

    public function createFromApi(User $user, array $data): Complaint
    {
        return $this->complaintRepository->create([
            'user_id'             => $user->id,
            'listing_id'          => $data['listing_id'],
            'complaint_reason_id' => $data['complaint_reason_id'],
            'text'                => $data['text'] ?? null,
            'status'              => 'new',
        ]);
    }
}
