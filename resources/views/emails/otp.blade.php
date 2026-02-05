<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification OTP</title>
</head>
<body>
    <h2>Email Verification</h2>

    <p>Your One-Time Password (OTP) is:</p>

    <h1 style="letter-spacing: 4px;">
        {{ $otp }}
    </h1>

    <p>This OTP will expire in <strong>5 minutes</strong>.</p>

    <p>If you did not request this, please ignore this email.</p>
</body>
</html>

