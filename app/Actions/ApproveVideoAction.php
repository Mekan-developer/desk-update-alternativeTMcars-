<?php

namespace App\Actions;

use App\Models\Video;
use App\Services\VideoService;

class ApproveVideoAction
{
    public function __construct(
        private readonly VideoService $videoService,
    ) {}

    public function execute(Video $video): void
    {
        $this->videoService->approve($video);
    }
}
