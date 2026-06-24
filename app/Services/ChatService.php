<?php

namespace App\Services;

use App\Events\NewMessageEvent;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function __construct(
        private readonly ChatRepositoryInterface $chatRepository,
    ) {}

    public function dialogs(): LengthAwarePaginator
    {
        return $this->chatRepository->getDialogs();
    }

    public function messages(int $userId): Collection
    {
        return $this->chatRepository->getMessages($userId);
    }

    public function reply(User $user, string $content): Message
    {
        $message = $this->chatRepository->createMessage([
            'user_id'     => $user->id,
            'sender_type' => 'admin',
            'content'     => $content,
            'is_read'     => false,
        ]);

        broadcast(new NewMessageEvent($message))->toOthers();

        return $message;
    }

    public function markRead(int $userId): void
    {
        $this->chatRepository->markAsRead($userId);
    }

    public function countUnread(): int
    {
        return $this->chatRepository->countUnread();
    }
}
