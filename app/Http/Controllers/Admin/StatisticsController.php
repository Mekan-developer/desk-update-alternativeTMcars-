<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Review;
use App\Models\Tariff;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ? now()->parse($request->from)->startOfDay() : now()->startOfDay();
        $to   = $request->to   ? now()->parse($request->to)->endOfDay()     : now()->endOfDay();

        return Inertia::render('Statistics/Index', [
            'stats' => [
                'users' => [
                    'total'   => User::where('role', 'user')->count(),
                    'period'  => User::where('role', 'user')->whereBetween('created_at', [$from, $to])->count(),
                    'blocked' => User::where('role', 'user')->where('status', 'blocked')->count(),
                ],
                'listings' => [
                    'total'    => Listing::count(),
                    'period'   => Listing::whereBetween('created_at', [$from, $to])->count(),
                    'pending'  => Listing::where('status', 'pending')->count(),
                    'approved' => Listing::where('status', 'approved')->count(),
                    'rejected' => Listing::where('status', 'rejected')->count(),
                ],
                'videos' => [
                    'total'    => Video::count(),
                    'approved' => Video::where('status', 'approved')->count(),
                    'likes'    => Video::sum('likes_count'),
                ],
            ],
            'tariffStats' => Tariff::withCount('users')->get()->map(fn($t) => [
                'name'   => $t->name,
                'count'  => $t->users_count,
                'pct'    => User::where('role', 'user')->count() > 0
                    ? round($t->users_count / User::where('role', 'user')->count() * 100)
                    : 0,
            ]),
            'filters' => $request->only('from', 'to'),
        ]);
    }
}
