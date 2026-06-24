<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintReason extends Model
{
    protected $fillable = ['name_ru', 'name_tk', 'is_active'];

    public function complaints() { return $this->hasMany(Complaint::class); }
}
