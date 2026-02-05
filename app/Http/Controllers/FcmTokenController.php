<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        Log::info('FCM TOKEN STORE HIT', [
            'user_id' => auth()->id(),
            'token' => $request->token,
        ]);

        if (!auth()->check()) {
            Log::warning('FCM TOKEN STORE: unauthenticated');
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'token' => 'required|string',
        ]);

        auth()->user()->update([
            'fcm_token' => $request->token,
        ]);

        Log::info('FCM TOKEN SAVED', [
            'user_id' => auth()->id(),
        ]);

        return response()->json(['saved' => true]);
    }
}
