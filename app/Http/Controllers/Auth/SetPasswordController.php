<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SetPasswordController extends Controller
{
    public function setPasswordold(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $user = User::where('email', $request->email)->first();

        // Email must be verified
        if (!$user->email_verified) {
            return response()->json([
                'message' => 'Email not verified'
            ], 403);
        }

        // Password should be set only once
        if ($user->password) {
            return response()->json([
                'message' => 'Password already set'
            ], 400);
        }

        // Password must not contain email or username
        $emailUsername = Str::before($user->email, '@');

        if (
            Str::contains(strtolower($request->password), strtolower($user->email)) ||
            Str::contains(strtolower($request->password), strtolower($emailUsername))
        ) {
            return response()->json([
                'message' => 'Password cannot contain your email'
            ], 422);
        }

        // Save password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password set successfully'
        ]);
    }
    public function setPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'password' => ['required', 'confirmed', new StrongPassword],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user->email_verified) {
        return response()->json([
            'message' => 'Email not verified'
        ], 403);
    }

    if ($user->password) {
        return response()->json([
            'message' => 'Password already set'
        ], 400);
    }

    $emailUsername = Str::before($user->email, '@');

    if (
        Str::contains(strtolower($request->password), strtolower($user->email)) ||
        Str::contains(strtolower($request->password), strtolower($emailUsername))
    ) {
        return response()->json([
            'message' => 'Password cannot contain your email'
        ], 422);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'Password set successfully'
    ], 200);
}

}
