<?php

namespace App\Listeners;

use App\Events\ListingRejected;
use App\Jobs\SendPushNotificationJob;

class SendListingRejectedPush
{
    public function handle(ListingRejected $event): void
    {
        $user = $event->listing->user;

        if (! $user?->fcm_token) {
            return;
        }

        SendPushNotificationJob::dispatch(
            $user->fcm_token,
            __('messages.push_listing_rejected_title'),
            __('messages.push_listing_rejected_body', ['title' => $event->listing->title]),
            'listing_rejected',
            'listing',
            $event->listing->id,
        );
    }
}
