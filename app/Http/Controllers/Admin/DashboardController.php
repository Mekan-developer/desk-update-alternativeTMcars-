<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Review;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'users'            => User::where('role', 'user')->count(),
            'listings'         => Listing::count(),
            'listings_pending' => Listing::where('status', 'pending')->count(),
            'videos'           => Video::count(),
            'videos_pending'   => Video::where('status', 'pending')->count(),
            'complaints_new'   => Complaint::where('status', 'new')->count(),
        ];

        $charts = [
            'users_7d'    => User::where('role', 'user')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->pluck('total', 'date'),
            'listings_7d' => Listing::where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->pluck('total', 'date'),
        ];

        $topCategories = \App\Models\Category::withCount('listings')
            ->orderByDesc('listings_count')
            ->take(5)
            ->get();

        $recentListings = Listing::with('user', 'category', 'region')
            ->latest()
            ->take(6)
            ->get();

        $recentComplaints = Complaint::with('user', 'listing', 'complaintReason')
            ->where('status', 'new')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', compact(
            'stats', 'charts', 'topCategories', 'recentListings', 'recentComplaints'
        ));
    }
}
