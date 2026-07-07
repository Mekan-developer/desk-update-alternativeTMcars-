<?php

namespace App\Actions;

use App\Repositories\Interfaces\SmsCodeRepositoryInterface;
use Illuminate\Validation\ValidationException;

class VerifySmsCodeAction
{
    public function __construct(
        private readonly SmsCodeRepositoryInterface $smsCodeRepository,
    ) {}

    public function execute(string $phone, string $code): void
    {
        $smsCode = $this->smsCodeRepository->findActive($phone);

        if (! $smsCode) {
            throw ValidationException::withMessages([
                'code' => __('messages.sms_code_invalid'),
            ]);
        }

        if ($smsCode->attempts >= config('sms.max_attempts')) {
            $this->smsCodeRepository->markUsed($smsCode);

            throw ValidationException::withMessages([
                'code' => __('messages.sms_too_many_attempts'),
            ]);
        }

        if (! hash_equals($smsCode->code, $code)) {
            $this->smsCodeRepository->incrementAttempts($smsCode);

            throw ValidationException::withMessages([
                'code' => __('messages.sms_code_invalid'),
            ]);
        }

        $this->smsCodeRepository->markUsed($smsCode);
    }
}
