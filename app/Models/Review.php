<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'target_user_id', 'listing_id', 'text', 'rating', 'status', 'rejection_reason_id'];

    public function user()            { return $this->belongsTo(User::class); }
    public function targetUser()      { return $this->belongsTo(User::class, 'target_user_id'); }
    public function listing()         { return $this->belongsTo(Listing::class); }
    public function rejectionReason() { return $this->belongsTo(RejectionReason::class); }
}
