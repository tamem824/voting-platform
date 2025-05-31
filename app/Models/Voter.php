<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;



class Voter extends  Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name', 'phone', 'membership_number', 'has_voted', 'is_admin',
    ];

    public function votes() :HasMany
    {
        return $this->hasMany(Vote::class);
    }
    public static function hasVoted($id)
    {
        $voter = Voter::findOrFail($id);
        return $voter->has_voted;
    }

}
