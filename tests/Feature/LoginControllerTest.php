<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_shows_login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_login_with_invalid_credentials_fails()
    {
        $response = $this->post('/login', [
            'membership_number' => 'wrong',
            'phone' => 'wrong',
        ]);


        $response->assertStatus(302);


        $response->assertSessionHas('error', 'البيانات غير صحيحة');
    }


    public function test_send_code_and_verify()
    {
        $voter = Voter::factory()->create();

        // ارسال الكود
        $response = $this->post('/login', [
            'membership_number' => $voter->membership_number,
            'phone' => $voter->phone,
            'send_code' => true,
        ]);

        $response->assertSessionHas('success');

        // جلب الـ voter بعد تحديث الكود
        $voter->refresh();

        // تحقق من الكود
        $response = $this->post('/login', [
            'membership_number' => $voter->membership_number,
            'phone' => $voter->phone,
            'code' => $voter->verification_code,
        ]);

        $response->assertRedirect('/votes/vote');
        $this->assertAuthenticatedAs($voter);
    }
}
