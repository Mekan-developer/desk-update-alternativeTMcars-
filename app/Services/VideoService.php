<?php

namespace App\Services;

use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VideoService
{
    public function __construct(
        private readonly VideoRepositoryInterface $videoRepository,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->videoRepository->paginate($filters);
    }

    public function counts(): array
    {
        return [
            'pending'  => $this->videoRepository->countByStatus('pending'),
            'approved' => $this->videoRepository->countByStatus('approved'),
            'rejected' => $this->videoRepository->countByStatus('rejected'),
        ];
    }

    public function approve(Video $video): void
    {
        $this->videoRepository->update($video, [
            'status'              => 'approved',
            'rejection_reason_id' => null,
        ]);
    }

    public function reject(Video $video, int $rejectionReasonId): void
    {
        $this->videoRepository->update($video, [
            'status'              => 'rejected',
            'rejection_reason_id' => $rejectionReasonId,
        ]);
    }

    public function delete(Video $video): void
    {
        $this->videoRepository->delete($video);
    }
}
