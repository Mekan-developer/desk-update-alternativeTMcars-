<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    public function __invoke(MonitoringService $monitoring): JsonResponse
    {
        return response()->json($monitoring->getStatus());
    }
}
