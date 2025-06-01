<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {

        Candidate::factory()->count(5)->create([
            'type' => 'president'
        ]);

// إنشاء 10 أعضاء
        Candidate::factory()->count(10)->create([
            'type' => 'member'
        ]);
    }
}
