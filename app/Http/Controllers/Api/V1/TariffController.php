<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TariffResource;
use App\Services\TariffService;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function __construct(
        private readonly TariffService $tariffService,
    ) {}

    /**
     * Текущий тариф пользователя и остаток лимитов.
     * GET /api/v1/profile/tariff
     *
     * @authenticated
     */
    public function show(Request $request)
    {
        $summary = $this->tariffService->currentForUser($request->user());

        return response()->json([
            'data' => [
                'tariff'     => $summary['tariff'] ? new TariffResource($summary['tariff']) : null,
                'expires_at' => $summary['expires_at']?->toIso8601String(),
                'remaining'  => $summary['remaining'],
            ],
            'message' => 'Success',
        ]);
    }
}
