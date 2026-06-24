<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = ['name', 'listings_limit', 'videos_limit', 'boosts_limit', 'duration_days', 'is_free', 'is_active'];

    public function users() { return $this->hasMany(User::class); }
}
