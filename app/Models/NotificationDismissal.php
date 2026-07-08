<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDismissal extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'key'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
