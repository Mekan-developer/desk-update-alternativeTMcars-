<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RegionResource;
use App\Services\RegionService;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionService $regionService,
    ) {}

    /**
     * Список активных регионов с городами и районами для мобильного приложения.
     * GET /api/v1/regions
     */
    public function index()
    {
        return response()->json([
            'data' => RegionResource::collection($this->regionService->activeListWithDistricts()),
            'message' => 'Success',
        ]);
    }
}
