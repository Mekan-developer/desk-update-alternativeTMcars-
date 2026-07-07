<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendTestSmsRequest;
use App\Services\Sms\SmsGatewayStatusService;
use App\Services\Sms\SmsSenderInterface;
use Illuminate\Http\JsonResponse;

class SmsGatewayController extends Controller
{
    public function status(SmsGatewayStatusService $smsStatus): JsonResponse
    {
        return response()->json($smsStatus->resolve());
    }

    public function test(SendTestSmsRequest $request)
    {
        $phone = $request->validated('phone') ?: $request->user()->phone;

        try {
            app(SmsSenderInterface::class)->send($phone, 'Тестовое сообщение с панели администратора');

            return back()->with('toast', ['type' => 'success', 'message' => __('messages.sms_test_sent')]);
        } catch (\Throwable $e) {
            return back()->with('toast', ['type' => 'error', 'message' => __('messages.sms_test_failed') . ': ' . $e->getMessage()]);
        }
    }
}
