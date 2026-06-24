<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title_ru', 'title_tk', 'body_ru', 'body_tk', 'image_path', 'type', 'link_type', 'link_id', 'is_published', 'published_at'];

    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];
}
