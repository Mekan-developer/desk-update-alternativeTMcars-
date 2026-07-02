<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name_ru', 'name_tk', 'is_hidden'];

    protected $casts = ['is_hidden' => 'boolean'];

    public function cities()   { return $this->hasMany(City::class); }
    public function users()    { return $this->hasMany(User::class); }
    public function listings() { return $this->hasMany(Listing::class); }
}
