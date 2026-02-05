<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailOtpMail;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Validator;
use Throwable;
class EmailVerificationController extends Controller
{
    
public function sendOtpold(Request $request, EmailOtpService $service)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $service->send($request->email);

    return response()->json([
        'message' => 'OTP sent successfully'
    ]);
}


public function sendOtp(Request $request, EmailOtpService $service)
{
    try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ⚠️ Wrap service call defensively
        try {
            $service->send($request->email);
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->withErrors([
                    'error' => 'Failed to send OTP. Please try again.'
                ]);
        }

        return redirect()
            ->back()
            ->with('success', 'OTP sent successfully');

    } catch (Throwable $e) {
        // FINAL controller safety net
        return redirect()
            ->back()
            ->withErrors([
                'error' => 'Unexpected error occurred.'
            ]);
    }
}


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email'], 404);
        }

        $otpRow = EmailOtp::where('user_id', $user->id)->first();

        if (!$otpRow || now()->greaterThan($otpRow->expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        if ($otpRow->attempts >= 5) {
            return response()->json(['message' => 'Too many attempts'], 429);
        }

        if (!Hash::check($request->otp, $otpRow->otp)) {
            $otpRow->increment('attempts');
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // SUCCESS
        $user->update([
            'email_verified' => true,
            'email_verified_at' => now(),
        ]);

        $otpRow->delete();

        return response()->json([
            'message' => 'Email veriified successfully'
        ]);
    }
}
