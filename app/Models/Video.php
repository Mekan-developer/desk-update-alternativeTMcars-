<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['user_id', 'title', 'path', 'tags', 'status', 'rejection_reason_id', 'likes_count', 'views'];

    protected $casts = ['tags' => 'array'];

    public function user()            { return $this->belongsTo(User::class); }
    public function rejectionReason() { return $this->belongsTo(RejectionReason::class); }
    public function likes()           { return $this->hasMany(VideoLike::class); }
}
