<?php

namespace App\Http\Middleware;

use App\Models\Complaint;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Review;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth'  => ['user' => $request->user()],
            'flash' => fn () => ['toast' => $request->session()->get('toast')],
            'counts' => fn () => $request->user() ? [
                'newUsers'        => User::where('role', 'user')->where('created_at', '>=', now()->subDay())->count(),
                'pendingListings' => Listing::where('status', 'pending')->count(),
                'pendingVideos'   => Video::where('status', 'pending')->count(),
                'unreadChats'     => Message::where('sender', 'user')->where('is_read', false)->distinct('user_id')->count('user_id'),
                'newComplaints'   => Complaint::where('status', 'new')->count(),
                'pendingReviews'  => Review::where('status', 'pending')->count(),
            ] : [],
        ];
    }
}
