<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Validator;
use App\Rules\StrongPassword;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('assignmentviews.Auth.register');
    }

    public function sendOtpold(Request $request, EmailOtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // store email for next step
        session(['email' => $request->email]);

        $otpService->send($request->email);

        return redirect()
            ->route('register.fill-otp')
            ->with('success', 'OTP sent successfully');
    }
public function sendOtp(Request $request, EmailOtpService $otpService)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }

    $email = $request->email;

    // Check if user exists
    $user = User::where('email', $email)->first();

    if ($user && $user->email_verified) {
        return back()->withErrors([
            'email' => 'Email is already verified. Please login.'
        ]);
    }

    // If user does not exist, create one (optional – depends on your flow)
    if (!$user) {
        $user = User::create([
            'email' => $email,
        ]);
    }

    // Check last OTP (rate limit)
    $lastOtp = EmailOtp::where('user_id', $user->id)
        ->latest()
        ->first();

    if ($lastOtp) {
        $secondsLeft = now()->diffInSeconds($lastOtp->created_at);

        if ($secondsLeft < 60) {
            return back()->withErrors([
                'email' => 'Please wait before requesting another OTP.'
            ]);
        }
    }

   try {
    session(['email' => $email]);
    $otpService->send($email);
} catch (\Throwable $e) {
    dd($e->getMessage(), $e->getTraceAsString());
}


    return redirect()
        ->route('register.fill-otp')
        ->with('success', 'OTP sent successfully');
}


    public function fillregistrationotp()
    {
        if (!session('email')) {
            return redirect()->route('register')->with('error', 'Session expired');
        }

        return view('assignmentviews.Auth.registerotp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Invalid email');
        }
$otpRow = EmailOtp::where('user_id', $user->id)->first();

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
        $user->update([
            'email_verified' =>1,
            'email_verified_at' => now(),
        ]);

        $otpRow->delete();

        return redirect()
    ->route('register.setpassword', ['email' => $request->email])
    ->with('success', 'Email verified successfully');

    }
  
  public function setPasswordold(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | GET REQUEST → Show Set Password Page
    |--------------------------------------------------------------------------
    */
    if ($request->isMethod('get')) {

        $email = $request->query('email');

        if (!$email) {
            return redirect()
                ->route('register')
                ->with('error', 'Invalid access to password setup');
        }

        return view('assignmentviews.Auth.setpassword', [
            'email' => $email
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST REQUEST → Handle Password Submission
    |--------------------------------------------------------------------------
    */
    $request->validate([
        'email' => 'required|email',
        'password' => ['required', 'confirmed', new StrongPassword],
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'Invalid user');
    }

    if (!$user->email_verified) {
        return back()->with('error', 'Email not verified');
    }

    if ($user->password) {
        return back()->with('error', 'Password already set');
    }

    $emailUsername = Str::before($user->email, '@');

    if (
        Str::contains(strtolower($request->password), strtolower($user->email)) ||
        Str::contains(strtolower($request->password), strtolower($emailUsername))
    ) {
        return back()->with('error', 'Password cannot contain your email');
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return redirect()
        ->route('login')
        ->with('success', 'Password set successfully. Kindly Login');
}

public function setPassword(Request $request)
{
    // ------------------------
    // GET → Show page
    // ------------------------
    if ($request->isMethod('get')) {

        $email = $request->query('email');

        if (!$email) {
            return redirect()
                ->route('register')
                ->with('error', 'Invalid or expired password setup link');
        }

        return view('assignmentviews.Auth.setpassword', compact('email'));
    }

    // ------------------------
    // POST → Submit password
    // ------------------------
    try {

        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        if (!$user->email_verified) {
            return back()->with('error', 'Please verify your email first');
        }

        if ($user->password) {
            return back()->with('error', 'Password already set. Please login');
        }

        $emailUsername = Str::before($user->email, '@');

        if (
            Str::contains(strtolower($request->password), strtolower($user->email)) ||
            Str::contains(strtolower($request->password), strtolower($emailUsername))
        ) {
            return back()->with('error', 'Password should not contain your email');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Password set successfully. Please login.');

    } catch (\Illuminate\Validation\ValidationException $e) {

        return back()
            ->withErrors($e->validator)
            ->withInput();

    } catch (\Exception $e) {

        return back()->with('error', 'Something went wrong. Please try again.');
    }
}

}
