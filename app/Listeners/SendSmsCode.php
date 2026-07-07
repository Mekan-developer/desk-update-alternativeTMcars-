<?php

namespace App\Listeners;

use App\Events\SmsCodeRequested;
use App\Services\Sms\SmsSenderInterface;

class SendSmsCode
{
    public function __construct(
        private readonly SmsSenderInterface $smsSender,
    ) {}

    public function handle(SmsCodeRequested $event): void
    {
        $this->smsSender->send(
            $event->phone,
            __('messages.sms_code_text', ['code' => $event->code]),
        );
    }
}
