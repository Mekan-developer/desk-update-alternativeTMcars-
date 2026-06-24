<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        $dialogs = User::where('role', 'user')
            ->whereHas('messages')
            ->with(['messages' => fn($q) => $q->latest()->take(1)])
            ->withCount(['messages as unread_count' => fn($q) => $q->where('sender', 'user')->where('is_read', false)])
            ->latest('updated_at')
            ->paginate(30);

        return Inertia::render('Chat/Index', [
            'dialogs' => $dialogs,
        ]);
    }

    public function show(User $user)
    {
        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        Message::where('user_id', $user->id)->where('sender', 'user')->update(['is_read' => true]);

        $dialogs = User::where('role', 'user')
            ->whereHas('messages')
            ->with(['messages' => fn($q) => $q->latest()->take(1)])
            ->withCount(['messages as unread_count' => fn($q) => $q->where('sender', 'user')->where('is_read', false)])
            ->latest('updated_at')
            ->get();

        return Inertia::render('Chat/Show', [
            'chatUser' => $user,
            'messages' => $messages,
            'dialogs'  => $dialogs,
        ]);
    }

    public function reply(Request $request, User $user)
    {
        $data = $request->validate(['text' => 'required|string|max:2000']);

        Message::create([
            'user_id'  => $user->id,
            'sender'   => 'admin',
            'admin_id' => $request->user()->id,
            'text'     => $data['text'],
        ]);

        return back();
    }

    public function markRead(User $user)
    {
        Message::where('user_id', $user->id)->where('sender', 'user')->update(['is_read' => true]);

        return back();
    }
}
