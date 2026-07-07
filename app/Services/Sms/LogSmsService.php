<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Временная dev-реализация: пишет SMS в laravel.log вместо реальной отправки.
 * Продовая реализация через локальный модем — LocalModemSmsService (та же сигнатура),
 * после её появления заменить binding в AppServiceProvider.
 */
class LogSmsService implements SmsSenderInterface
{
    public function send(string $phone, string $message): void
    {
        Log::info("SMS to {$phone}: {$message}");
    }
}
