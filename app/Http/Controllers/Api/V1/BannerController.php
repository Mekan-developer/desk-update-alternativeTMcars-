<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BannerResource;
use App\Services\BannerService;

class BannerController extends Controller
{
    public function __construct(
        private readonly BannerService $bannerService,
    ) {}

    /**
     * Активные баннеры для мобильного приложения
     * GET /api/v1/banners
     */
    public function index()
    {
        return response()->json([
            'data' => BannerResource::collection($this->bannerService->activeForApi()),
        ]);
    }
}
