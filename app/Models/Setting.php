<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['starting_vote', 'ending_vote', 'is_active'];

    protected $casts = [
        'starting_vote' => 'datetime',
        'ending_vote' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function isVotingActive()
    {
        $setting = self::where('is_active', true)->first();
        if (!$setting) return false;

        $now = now();
        return $now->between($setting->starting_vote, $setting->ending_vote);
    }
}
