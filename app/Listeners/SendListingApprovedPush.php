<?php

namespace App\Listeners;

use App\Events\ListingApproved;
use App\Jobs\SendPushNotificationJob;

class SendListingApprovedPush
{
    public function handle(ListingApproved $event): void
    {
        $user = $event->listing->user;

        if (! $user?->fcm_token) {
            return;
        }

        SendPushNotificationJob::dispatch(
            $user->fcm_token,
            __('messages.push_listing_approved_title'),
            __('messages.push_listing_approved_body', ['title' => $event->listing->title]),
            'listing_approved',
            'listing',
            $event->listing->id,
        );
    }
}
