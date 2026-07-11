<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    /** Удалять из приложения можно только свой ролик */
    public function delete(User $user, Video $video): bool
    {
        return $video->user_id === $user->id;
    }
}
