<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatReplyRequest;
use App\Models\User;
use App\Services\ChatService;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
    ) {}

    public function index()
    {
        return Inertia::render('Chat/Index', [
            'dialogs' => $this->chatService->dialogs(),
        ]);
    }

    public function show(User $user)
    {
        $this->chatService->markRead($user->id);

        return Inertia::render('Chat/Show', [
            'chatUser' => $user,
            'messages' => $this->chatService->messages($user->id),
            'dialogs'  => $this->chatService->dialogs(),
        ]);
    }

    public function reply(ChatReplyRequest $request, User $user)
    {
        $this->chatService->reply($user, $request->validated('content'));

        return back();
    }

    public function markRead(User $user)
    {
        $this->chatService->markRead($user->id);

        return back();
    }
}
