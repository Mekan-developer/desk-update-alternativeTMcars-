<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['region_id', 'name_ru', 'name_tk'];

    public function region()   { return $this->belongsTo(Region::class); }
    public function users()    { return $this->hasMany(User::class); }
    public function listings() { return $this->hasMany(Listing::class); }
}
