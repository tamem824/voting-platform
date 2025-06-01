<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voter;
use Carbon\Carbon;

class VoterSeeder extends Seeder
{
    public function run(): void
    {
        Voter::create([
            'name' => 'voter',
            'phone' => '0500000000',
            'membership_number' => 'MEM123456',
            'has_voted' => false,
            'is_admin' => true,
            'verification_code' => 123456,
            'code_expires_at' => Carbon::now()->addHours(24),
        ]);
    }
}
