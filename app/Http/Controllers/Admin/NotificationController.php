<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DismissNotificationRequest;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function dismiss(DismissNotificationRequest $request)
    {
        $this->notificationService->dismiss($request->user(), $request->validated('key'));

        return back();
    }
}
