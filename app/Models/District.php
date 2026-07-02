<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['city_id', 'name_ru', 'name_tk', 'is_hidden'];

    protected $casts = ['is_hidden' => 'boolean'];

    public function city() { return $this->belongsTo(City::class); }
}
