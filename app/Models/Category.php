<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public const MAX_LEVEL = 3;

    protected $fillable = ['parent_id', 'name_ru', 'name_tk', 'slug', 'icon_path', 'level', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected $appends = ['icon_url'];

    public function parent()   { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id')->orderBy('order'); }
    public function listings() { return $this->hasMany(Listing::class); }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon_path ? '/storage/'.$this->icon_path : null;
    }
}
