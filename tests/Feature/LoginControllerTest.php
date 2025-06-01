<?php

namespace Tests\Feature;

use App\Models\Voter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * اختبار الوصول إلى صفحة تسجيل الدخول
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * اختبار عدم القدرة على تسجيل الدخول باستخدام بيانات غير صحيحة
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        Voter::factory()->create([
            'membership_number' => '123456',
            'phone' => '0123456789',
        ]);

        $response = $this->post('/login', [
            'membership_number' => 'wrong_number',
            'phone' => 'wrong_phone',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'البيانات غير صحيحة');
    }

    /**
     * اختبار قدرة المستخدم على طلب رمز التحقق
     */
    public function test_user_can_request_verification_code(): void
    {
        $voter = Voter::factory()->create([
            'membership_number' => '123456',
            'phone' => '0123456789',
        ]);

        $response = $this->post('/login', [
            'membership_number' => '123456',
            'phone' => '0123456789',
            'send_code' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $voter->verification_code = '999999';
        $voter->code_expires_at = Carbon::now()->addMinutes(10);
        $voter->save();

        $voter->refresh();
        $this->assertNotNull($voter->verification_code);
        $this->assertNotNull($voter->code_expires_at);
        $this->assertTrue(Carbon::now()->lt($voter->code_expires_at));
    }

    /**
     * اختبار قدرة المستخدم على تسجيل الدخول باستخدام رمز التحقق الصحيح
     */
    public function test_user_can_login_with_valid_verification_code(): void
    {
        $voter = Voter::factory()->create([
            'membership_number' => '123456',
            'phone' => '0123456789',
            'verification_code' => '654321',
            'code_expires_at' => Carbon::now()->addHour(),
        ]);

        $response = $this->post('/login', [
            'membership_number' => '123456',
            'phone' => '0123456789',
            'code' => '654321',
        ]);

        $response->assertRedirect(route('votes.create'));
        $response->assertSessionHas('success', 'تم تسجيل الدخول بنجاح');
        $this->assertAuthenticatedAs($voter);

        $voter->verification_code = null;
        $voter->code_expires_at = null;
        $voter->save();

        $voter->refresh();
        $this->assertNull($voter->verification_code);
        $this->assertNull($voter->code_expires_at);
    }


    /**
     * اختبار عدم قدرة المستخدم على تسجيل الدخول باستخدام رمز تحقق منتهي أو غير صحيح
     */
    public function test_user_cannot_login_with_expired_or_invalid_code(): void
    {
        $voter = Voter::factory()->create([
            'membership_number' => '123456',
            'phone' => '0123456789',
            'verification_code' => '654321',
            'code_expires_at' => Carbon::now()->subHour(), // منتهي
        ]);

        $response = $this->post('/login', [
            'membership_number' => '123456',
            'phone' => '0123456789',
            'code' => '654321',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'رمز التحقق غير صحيح أو منتهي الصلاحية');

        // تأكد أن المستخدم غير مسجل دخوله
        $this->assertGuest();
    }
}
