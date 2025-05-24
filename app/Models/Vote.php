<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;
    protected $fillable = [
        'voter_id', 'candidate_id',
    ];

    public function voter() :BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }

    public function candidate():BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
