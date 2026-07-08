<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMessageRequest;
use App\Http\Resources\Api\V1\MessageResource;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
    ) {}

    /**
     * История переписки с поддержкой (единственный диалог пользователя).
     * GET /api/v1/chat
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        return response()->json([
            'data' => MessageResource::collection($this->chatService->messages($request->user()->id)),
        ]);
    }

    /**
     * Отправка сообщения в поддержку.
     * POST /api/v1/chat
     *
     * @authenticated
     */
    public function store(StoreMessageRequest $request)
    {
        $message = $this->chatService->sendFromUser($request->user(), $request->validated('text'));

        return response()->json([
            'data'    => new MessageResource($message),
            'message' => __('messages.message_sent'),
        ], 201);
    }

    /**
     * Отметить ответы админа прочитанными (пользователь открыл чат).
     * PATCH /api/v1/chat/read
     *
     * @authenticated
     */
    public function markRead(Request $request)
    {
        $this->chatService->markReadByUser($request->user()->id);

        return response()->json([
            'message' => __('messages.chat_marked_read'),
        ]);
    }
}
