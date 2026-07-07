<?php

namespace App\Providers;

use App\Events\ListingApproved;
use App\Events\ListingRejected;
use App\Events\SmsCodeRequested;
use App\Listeners\SendListingApprovedPush;
use App\Listeners\SendListingRejectedPush;
use App\Listeners\SendSmsCode;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ListingApproved::class => [
            SendListingApprovedPush::class,
        ],
        ListingRejected::class => [
            SendListingRejectedPush::class,
        ],
        SmsCodeRequested::class => [
            SendSmsCode::class,
        ],
    ];
}
