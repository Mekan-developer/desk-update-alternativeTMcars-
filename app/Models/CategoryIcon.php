<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryIcon extends Model
{
    protected $fillable = ['slug', 'path', 'is_system'];

    protected $casts = ['is_system' => 'boolean'];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return '/storage/'.$this->path;
    }
}
