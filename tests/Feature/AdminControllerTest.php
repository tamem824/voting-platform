<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\VoteLog;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Voter::factory()->create(['is_admin' => true]);
        $this->regularUser = Voter::factory()->create(['is_admin' => false]);
    }

    public function test_admin_routes_require_authentication()
    {
        $routes = [
            'admin.voters',
            'admin.settings',
            'admin.vote_logs',
            'admin.voters.show' => ['voter' => 1],
        ];

        foreach ($routes as $route => $parameters) {
            if (is_numeric($route)) {
                $route = $parameters;
                $response = $this->get(route($route));
            } else {
                $response = $this->get(route($route, $parameters));
            }

            $response->assertRedirect('/login');
        }
    }

    public function test_admin_routes_require_admin_privileges()
    {
        $this->actingAs($this->regularUser);

        $routes = [
            'admin.voters',
            'admin.settings',
            'admin.vote_logs',
            'admin.voters.show' => ['voter' => 1],
        ];

        foreach ($routes as $route => $parameters) {
            if (is_numeric($route)) {
                $route = $parameters;
                $response = $this->get(route($route));
            } else {
                $response = $this->get(route($route, $parameters));
            }

            $response->assertForbidden();
        }
    }

    public function test_admin_can_view_voted_voters()
    {
        $this->actingAs($this->admin);

        Voter::factory()->count(3)->create(['has_voted' => true]);
        Voter::factory()->count(2)->create(['has_voted' => false]);

        $response = $this->get(route('admin.voters'));

        $response->assertStatus(200);
        $this->assertCount(3, $response->viewData('voters'));
    }

    public function test_admin_can_view_settings_page()
    {
        $this->actingAs($this->admin);
        $this->seed(\Database\Seeders\SettingSeeder::class);
        $settings = Setting::first();

        $response = $this->get(route('admin.settings'));

        $response->assertViewHas('setting', $settings);
    }

    public function test_admin_can_update_settings()
    {
        $this->actingAs($this->admin);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $newData = [
            'starting_vote' => '2024-06-01 08:00:00',
            'ending_vote' => '2024-06-02 20:00:00',
            'is_active' => 0,
        ];

        $response = $this->post(route('admin.settings.update'), $newData);

        $this->assertDatabaseHas('settings', $newData);
        $response->assertRedirect()->assertSessionHas('success');
    }

    public function test_admin_can_view_vote_logs()
    {
        $this->actingAs($this->admin);

        VoteLog::create([
            'voter_id' => Voter::factory()->create()->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'device' => 'Test Device',
            'platform' => 'Test Platform',
            'browser' => 'Test Browser'
        ]);

        $response = $this->get(route('admin.vote_logs'));
        $response->assertViewHas('logs');
    }

    public function test_admin_can_view_voter_details()
    {
        $this->actingAs($this->admin);
        $voter = Voter::factory()->create();

        $response = $this->get(route('admin.voters.show', $voter));

        $response->assertViewHas('voter', $voter);
    }

    public function test_admin_gets_404_for_nonexistent_voter()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.voters.show', 9999));

        $response->assertStatus(404);
    }

    public function test_settings_update_requires_valid_data()
    {
        $this->actingAs($this->admin);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $invalidData = [
            'starting_vote' => 'not-a-valid-date',
            'ending_vote' => 'also-not-a-valid-date',
            'is_active' => 'not-a-boolean'
        ];

        $response = $this->post(route('admin.settings.update'), $invalidData);

        $response->assertSessionHasErrors([
            'starting_vote',
            'ending_vote',
            'is_active'
        ]);
    }

    public function test_update_settings_fails_if_ending_before_starting()
    {
        $this->actingAs($this->admin);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $response = $this->post(route('admin.settings.update'), [
            'starting_vote' => '2025-06-02 08:00:00',
            'ending_vote' => '2025-06-01 08:00:00',
            'is_active' => true
        ]);

        $response->assertSessionHasErrors('ending_vote');
    }
}
