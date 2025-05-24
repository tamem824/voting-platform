<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.login');
        }


        $request->validate([
            'membership_number' => 'required',
            'phone' => 'required',
        ]);

        $voter = Voter::where('membership_number', $request->membership_number)
            ->where('phone', $request->phone)
            ->first();

        if (!$voter) {
            return back()->withInput()->with('error', 'البيانات غير صحيحة');
        }


        if ($request->has('send_code')) {
            $code = random_int(100000, 999999);
            $voter->update([
                'verification_code' => $code,
                'code_expires_at' => Carbon::now()->addHours(24),
            ]);

            session(['voter_id' => $voter->id]);

            return back()->withInput()->with('success', 'تم إرسال الرمز. الرمز هو: ' . $code);
        }

// التحقق من الكود
        if (!$request->has('send_code')) {
            $request->validate([
                'code' => 'required',
            ]);

            if (
                $voter->verification_code === $request->code &&
                Carbon::now()->lessThanOrEqualTo($voter->code_expires_at)
            ) {
                Auth::login($voter);
                $voter->update([
                    'verification_code' => null,
                    'code_expires_at' => null,
                ]);
                return redirect('votes/vote')->with('success', 'تم تسجيل الدخول بنجاح');
            }

            return back()->withInput()->with('error', 'رمز التحقق غير صحيح أو منتهي الصلاحية');
        }
    }

}
