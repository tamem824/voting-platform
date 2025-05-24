<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Voter;
use App\Models\Candidate;

class VoteFactory extends Factory
{
    protected $model = \App\Models\Vote::class;

    public function definition()
    {
        return [
            'voter_id' => Voter::factory(),
            'candidate_id' => Candidate::factory(),
        ];
    }
}
