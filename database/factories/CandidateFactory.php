<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    protected $model = \App\Models\Candidate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['president', 'member']),
            'photo' => null,
            'bio' => $this->faker->paragraph,
        ];
    }
}
