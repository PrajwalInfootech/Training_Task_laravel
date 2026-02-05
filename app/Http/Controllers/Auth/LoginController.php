<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailOtpMail;
class LoginController extends Controller
{
   public function login(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | GET → Show Login Form
    |--------------------------------------------------------------------------
    */
    if ($request->isMethod('get')) {
        return view('assignmentviews.Auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | POST → Handle Login & Send OTP
    |--------------------------------------------------------------------------
    */
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (
        !$user ||
        !$user->password ||
        !Hash::check($request->password, $user->password)
    ) {
        return back()->with('error', 'Invalid credentials');
    }

    if (!$user->email_verified) {
        return back()->with('error', 'Email not verified');
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    EmailOtp::updateOrCreate(
        [
            'user_id' => $user->id,
            'type' => 'login',
        ],
        [
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'last_sent_at' => now(),
        ]
    );

    Mail::to($user->email)->send(new EmailOtpMail($otp));

//     return redirect()
//         ->route('dashboard')
//         ->with('email', $user->email)
//         ->with('success', 'OTP sent to your email');
// }
return redirect()
    ->route('login.otp')
    ->with('success', 'OTP sent to your email')
    ->with('email', $user->email);
}
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('login')
        ->with('success', 'Logged out successfully')
        ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

}
