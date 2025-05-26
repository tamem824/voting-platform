<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    protected $fillable = [
        'voter_id',
        'ip_address',
        'browser',
        'platform',
        'user_agent',
    ];

    // علاقة النموذج مع المصوت (Voter)
    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
