<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailOtpMail extends Mailable
{
    public function __construct(public string $otp) {}

    public function build()
    {
        return $this->subject('Your Email Verification OTP')
            ->view('emails.otp')
            ->with([
                'otp' => $this->otp,
            ]);
    }
}

