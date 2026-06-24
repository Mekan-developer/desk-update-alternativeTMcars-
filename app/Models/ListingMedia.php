<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingMedia extends Model
{
    protected $fillable = ['listing_id', 'path', 'type', 'order'];

    public function listing() { return $this->belongsTo(Listing::class); }
}
