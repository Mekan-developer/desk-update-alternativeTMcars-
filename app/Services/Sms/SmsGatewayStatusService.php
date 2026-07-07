<?php

namespace App\Services\Sms;

class SmsGatewayStatusService
{
    public function resolve(): array
    {
        $sender = app(SmsSenderInterface::class);

        if ($sender instanceof LocalModemSmsService) {
            return $sender->status();
        }

        return [
            'connected'    => false,
            'configured'   => false,
            'device'       => 'Dev: ' . class_basename($sender) . ' (запись в лог)',
            'last_sync_at' => null,
        ];
    }
}
