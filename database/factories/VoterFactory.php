<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoterFactory extends Factory
{
    protected $model = \App\Models\Voter::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->unique()->phoneNumber,
            'membership_number' => $this->faker->unique()->numerify('MEMBER####'),
            'has_voted' => false,
            'is_admin' => false,
            'verification_code' => null,
            'code_expires_at' => null,
        ];
    }
}
