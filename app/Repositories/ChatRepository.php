<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository implements ChatRepositoryInterface
{
    public function getDialogs(int $perPage = 25): LengthAwarePaginator
    {
        return User::where('role', 'user')
            ->whereHas('messages')
            ->withCount(['messages as unread_count' => fn($q) => $q->where('is_read', false)->where('sender_type', 'user')])
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->latest('updated_at')
            ->paginate($perPage);
    }

    public function getMessages(int $userId): Collection
    {
        return Message::where('user_id', $userId)->orderBy('created_at')->get();
    }

    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }

    public function markAsRead(int $userId): void
    {
        Message::where('user_id', $userId)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function countUnread(): int
    {
        return Message::where('sender_type', 'user')->where('is_read', false)
            ->distinct('user_id')->count('user_id');
    }
}
