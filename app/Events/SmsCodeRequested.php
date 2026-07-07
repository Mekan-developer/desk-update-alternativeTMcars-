<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SmsCodeRequested
{
    use Dispatchable;

    public function __construct(
        public readonly string $phone,
        public readonly string $code,
    ) {}
}
