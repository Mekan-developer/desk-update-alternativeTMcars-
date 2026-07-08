<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Models\Listing;
use App\Models\Message;
use App\Models\NotificationDismissal;
use App\Models\Review;
use App\Models\User;
use App\Models\Video;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function pendingItems(int $limitPerCategory = 20): array
    {
        $chatUserIds = Message::where('sender', 'user')
            ->where('is_read', false)
            ->select('user_id')
            ->distinct()
            ->latest('user_id')
            ->limit($limitPerCategory)
            ->pluck('user_id');

        return [
            'newUsers' => User::where('role', 'user')
                ->where('created_at', '>=', now()->subDay())
                ->latest()
                ->limit($limitPerCategory)
                ->get(['id', 'name', 'phone', 'created_at']),

            'pendingListings' => Listing::where('status', 'pending')
                ->latest()
                ->limit($limitPerCategory)
                ->get(['id', 'title', 'created_at']),

            'pendingVideos' => Video::where('status', 'pending')
                ->latest()
                ->limit($limitPerCategory)
                ->get(['id', 'title', 'created_at']),

            'unreadChats' => User::whereIn('id', $chatUserIds)
                ->get(['id', 'name', 'phone']),

            'newComplaints' => Complaint::where('status', 'new')
                ->with('listing:id,title')
                ->latest()
                ->limit($limitPerCategory)
                ->get(['id', 'listing_id', 'text', 'created_at']),

            'pendingReviews' => Review::where('status', 'pending')
                ->latest()
                ->limit($limitPerCategory)
                ->get(['id', 'text', 'created_at']),
        ];
    }

    public function dismissedKeys(int $userId): array
    {
        return NotificationDismissal::where('user_id', $userId)->pluck('key')->all();
    }

    public function dismiss(int $userId, string $key): void
    {
        NotificationDismissal::firstOrCreate(['user_id' => $userId, 'key' => $key]);
    }
}
