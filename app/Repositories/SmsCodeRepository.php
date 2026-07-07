<?php

namespace App\Repositories;

use App\Models\SmsCode;
use App\Repositories\Interfaces\SmsCodeRepositoryInterface;

class SmsCodeRepository implements SmsCodeRepositoryInterface
{
    public function create(string $phone, string $code, int $ttlSeconds): SmsCode
    {
        return SmsCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addSeconds($ttlSeconds),
        ]);
    }

    public function findActive(string $phone): ?SmsCode
    {
        return SmsCode::where('phone', $phone)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    public function findLatest(string $phone): ?SmsCode
    {
        return SmsCode::where('phone', $phone)->latest('id')->first();
    }

    public function markUsed(SmsCode $smsCode): void
    {
        $smsCode->update(['used_at' => now()]);
    }

    public function incrementAttempts(SmsCode $smsCode): void
    {
        $smsCode->increment('attempts');
    }

    public function invalidateActive(string $phone): void
    {
        SmsCode::where('phone', $phone)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }
}
