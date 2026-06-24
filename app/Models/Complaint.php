<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'listing_id', 'complaint_reason_id', 'text', 'status', 'resolved_by', 'resolution_note'];

    public function user()            { return $this->belongsTo(User::class); }
    public function listing()         { return $this->belongsTo(Listing::class); }
    public function complaintReason() { return $this->belongsTo(ComplaintReason::class); }
    public function resolver()        { return $this->belongsTo(User::class, 'resolved_by'); }
}
