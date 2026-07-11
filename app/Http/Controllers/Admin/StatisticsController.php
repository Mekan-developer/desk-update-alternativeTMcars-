<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatisticsFilterRequest;
use App\Services\StatisticsService;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticsService $statisticsService,
    ) {}

    public function index(StatisticsFilterRequest $request)
    {
        [$from, $to] = $this->statisticsService->resolvePeriod(
            $request->validated('period'),
            $request->validated('from'),
            $request->validated('to'),
        );

        return Inertia::render('Statistics/Index', [
            ...$this->statisticsService->getOverview($from, $to),
            'filters' => $request->only('period', 'from', 'to'),
        ]);
    }
}
