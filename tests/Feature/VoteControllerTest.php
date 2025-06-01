<?php
namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء إعدادات التصويت يدويًا بدون factory
        Setting::create([
            'starting_vote' => now()->subDay(),
            'ending_vote' => now()->addDay(),
            'is_active' => true,
        ]);
    }

    public function test_can_view_vote_form_with_correct_data()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        Candidate::factory()->count(2)->create(['type' => 'president']);
        Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->get(route('votes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('votes.create');
        $response->assertViewHasAll([
            'presidentCandidates',
            'memberCandidates',
            'candidateVotesCount',
            'totalVotesCount',
            'settings',
            'is_voted',
        ]);
    }

    public function test_cannot_vote_if_voting_is_inactive()
    {
        Setting::first()->update(['is_active' => false]);

        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHas('error', 'التصويت غير متاح حالياً');
    }

    public function test_cannot_vote_if_already_voted()
    {
        $voter = Voter::factory()->create(['has_voted' => true]);
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHas('error', 'لقد قمت بالتصويت مسبقًا.');
    }

    public function test_cannot_vote_with_invalid_president_id()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => 999,
            'member_ids' => Candidate::where('type', 'member')->pluck('id')->toArray(),
        ]);

        $response->assertSessionHasErrors(['president_id']);
    }

    public function test_cannot_vote_with_invalid_members_count()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(2)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHasErrors(['member_ids']);
    }

    public function test_user_can_vote_successfully()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member'])->pluck('id')->toArray();

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members,
        ]);

        $response->assertSessionHas('success', 'تم تسجيل التصويت بنجاح.');
        $this->assertDatabaseHas('votes', ['candidate_id' => $president->id, 'voter_id' => $voter->id]);
        $this->assertDatabaseCount('votes', 5);
        $this->assertEquals(1, $voter->fresh()->has_voted);
    }

    public function test_ten_voters_can_cast_50_votes()
    {
        $voters = Voter::factory()->count(10)->create();
        $candidates = Candidate::factory()->count(5)->create();

        foreach ($voters as $voter) {
            $randomCandidates = $candidates->random(5);
            foreach ($randomCandidates as $candidate) {
                Vote::factory()->create([
                    'voter_id' => $voter->id,
                    'candidate_id' => $candidate->id,
                ]);
            }
        }

        $this->assertDatabaseCount('votes', 50);
    }

    public function test_voters_can_select_president_and_members_randomly()
    {
        $presidents = Candidate::factory()->count(4)->create(['type' => 'president']);
        $members = Candidate::factory()->count(10)->create(['type' => 'member']);
        $voters = Voter::factory()->count(4000)->create();

        foreach ($voters as $voter) {
            $randomPresident = $presidents->random();
            $randomMembers = $members->random(4);

            Vote::factory()->create([
                'voter_id' => $voter->id,
                'candidate_id' => $randomPresident->id,
            ]);

            foreach ($randomMembers as $member) {
                Vote::factory()->create([
                    'voter_id' => $voter->id,
                    'candidate_id' => $member->id,
                ]);
            }
        }

        $this->assertDatabaseCount('votes', 20000);
    }

    public function test_cannot_vote_with_more_than_allowed_members()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(6)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHasErrors(['member_ids']);
    }

    public function test_can_vote_if_voting_is_active()
    {
        Setting::truncate();
        Setting::create([
            'starting_vote' => now()->subDay(),
            'ending_vote' => now()->addDays(3),
            'is_active' => true,
        ]);

        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHas('success', 'تم تسجيل التصويت بنجاح.');
    }

    public function test_it_returns_true_when_voting_is_active()
    {
        Setting::truncate();
        $setting = Setting::create([
            'starting_vote' => now()->subHour(),
            'ending_vote' => now()->addHour(),
            'is_active' => true,
        ]);

        $this->assertTrue($setting::isVotingActive());
    }

    public function test_it_returns_false_when_voting_is_outside_time_range()
    {
        Setting::truncate();
        $setting = Setting::create([
            'starting_vote' => now()->subDays(5),
            'ending_vote' => now()->subDays(1),
            'is_active' => true,
        ]);

        $this->assertFalse($setting->isVotingActive());
    }

    public function test_it_returns_false_when_voting_is_disabled()
    {
        Setting::truncate();
        $setting = Setting::create([
            'starting_vote' => now()->subHour(),
            'ending_vote' => now()->addHour(),
            'is_active' => false,
        ]);

        $this->assertFalse($setting->isVotingActive());
    }

    public function test_cannot_vote_when_voting_closed()
    {
        Setting::truncate();
        Setting::create([
            'starting_vote' => now()->subHour(),
            'ending_vote' => now()->addHour(),
            'is_active' => false,
        ]);

        $voter = Voter::factory()->create(['has_voted' => false]);
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertSessionHas('error');
    }

    public function test_cannot_vote_without_authentication()
    {
        $president = Candidate::factory()->create(['type' => 'president']);
        $members = Candidate::factory()->count(4)->create(['type' => 'member']);

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $members->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_cannot_vote_same_member_twice()
    {
        $voter = Voter::factory()->create();
        $this->actingAs($voter);

        $president = Candidate::factory()->create(['type' => 'president']);
        $member = Candidate::factory()->create(['type' => 'member']);
        $otherMembers = Candidate::factory()->count(3)->create(['type' => 'member']);

        $memberIds = $otherMembers->pluck('id')->toArray();
        $memberIds[] = $member->id;
        $memberIds[] = $member->id;

        $response = $this->post(route('votes.store'), [
            'president_id' => $president->id,
            'member_ids' => $memberIds,
        ]);

        $response->assertSessionHasErrors(['member_ids']);
    }
}
