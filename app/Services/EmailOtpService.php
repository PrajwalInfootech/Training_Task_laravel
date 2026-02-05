<?php
namespace App\Services;

use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\EmailOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class EmailOtpService
{
    public function send(string $email): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['email_verified' => false]
        );

        if ($user->email_verified) {
            throw new \Exception('Email already verified', 400);
        }

        $existingOtp = EmailOtp::where('user_id', $user->id)->first();

        if (
            $existingOtp &&
            $existingOtp->last_sent_at &&
            Carbon::parse($existingOtp->last_sent_at)->diffInSeconds(now()) < 60
        ) {
            throw new \Exception('Please wait before requesting another OTP', 429);
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
                'last_sent_at' => now(),
            ]
        );

        Mail::to($user->email)->send(
            new EmailOtpMail((string) $otp)
        );
    }
}
