<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $fillable = ['author_id', 'title_ru', 'title_tk', 'content_ru', 'content_tk', 'image', 'type', 'ad_link_type', 'ad_link_id', 'is_published', 'published_at'];

    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
