<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Voter;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteLoadTest extends TestCase
{
    use RefreshDatabase;

    public function test_many_votes()
    {

        $voters = Voter::factory()->count(10000)->create();


        $candidates = Candidate::factory()->count(5)->create();

        foreach ($voters as $voter) {
            $candidate = $candidates->random();


            $this->actingAs($voter);


            $response = $this->post(route('votes.store'), [
                'candidate_ids' => [$candidate->id],
            ]);

            $response->assertStatus(302); // إعادة توجيه ناجحة
        }
    }
}
