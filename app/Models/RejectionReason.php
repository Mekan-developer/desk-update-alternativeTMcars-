<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectionReason extends Model
{
    protected $fillable = ['name_ru', 'name_tk', 'type', 'is_active'];
}
