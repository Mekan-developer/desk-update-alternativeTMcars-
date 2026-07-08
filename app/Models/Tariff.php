<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = ['name_ru', 'name_tk', 'listings_limit', 'videos_limit', 'boost_limit', 'duration_days', 'is_free', 'is_active'];

    public function users() { return $this->hasMany(User::class); }
}
