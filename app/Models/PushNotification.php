<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = ['title', 'body', 'target', 'user_ids', 'filters', 'link_type', 'link_id', 'sent_count', 'sent_at', 'created_by'];

    protected $casts = ['user_ids' => 'array', 'filters' => 'array', 'sent_at' => 'datetime'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
