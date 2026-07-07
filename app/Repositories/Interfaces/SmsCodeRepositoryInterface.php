<?php

namespace App\Repositories\Interfaces;

use App\Models\SmsCode;

interface SmsCodeRepositoryInterface
{
    public function create(string $phone, string $code, int $ttlSeconds): SmsCode;

    /** Последний неиспользованный и непросроченный код для номера. */
    public function findActive(string $phone): ?SmsCode;

    /** Последний созданный код для номера (для проверки cooldown-а повторной отправки). */
    public function findLatest(string $phone): ?SmsCode;

    public function markUsed(SmsCode $smsCode): void;

    public function incrementAttempts(SmsCode $smsCode): void;

    /** Погасить все активные коды номера (перед выдачей нового). */
    public function invalidateActive(string $phone): void;
}
