<?php

namespace App\Actions;

use App\Models\Video;
use App\Services\VideoService;

class RejectVideoAction
{
    public function __construct(
        private readonly VideoService $videoService,
    ) {}

    public function execute(Video $video, int $rejectionReasonId): void
    {
        $this->videoService->reject($video, $rejectionReasonId);
    }
}
