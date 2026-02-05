<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginOtpController extends Controller
{
    public function verify(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GET → Show OTP Form
        |--------------------------------------------------------------------------
        */
        if ($request->isMethod('get')) {

            if (!session('email')) {
                return redirect()->route('login')
                    ->with('error', 'Session expired. Please login again.');
            }

            return view('assignmentviews.Auth.loginotp', [
                'email' => session('email')
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | POST → Verify OTP
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Invalid email');
        }

        $otpRow = EmailOtp::where([
            'user_id' => $user->id,
            'type'    => 'login',
        ])->first();

        if (!$otpRow) {
            return back()->with('error', 'OTP not found');
        }

        if (now()->greaterThan($otpRow->expires_at)) {
            return back()->with('error', 'OTP expired');
        }

        if ($otpRow->attempts >= 5) {
            return back()->with('error', 'Too many attempts');
        }

        if (!Hash::check($request->otp, $otpRow->otp)) {
            $otpRow->increment('attempts');
            return back()->with('error', 'Invalid OTP');
        }

        // ✅ SUCCESS
        Auth::login($user);
        $otpRow->delete();

        return redirect()
            ->route('analysis')
            ->with('success', 'Login successful');
    }
}
