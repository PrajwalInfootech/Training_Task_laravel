<?php
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
// use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginOtpController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Route::middleware('auth:sanctum')->post(
    '/auth/logout',
    [LogoutController::class, 'logout']
);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn (Request $request) => $request->user());
});

Route::post('/auth/login', [LoginController::class, 'login']);
Route::post('/auth/login/verify-otp', [LoginOtpController::class, 'verify']);


Route::post('/auth/set-password', [SetPasswordController::class, 'setPassword']);

Route::post('/verify-email/send-otp', [EmailVerificationController::class, 'sendOtp']);
Route::post('/verify-otp', [EmailVerificationController::class, 'verifyOtp']);

Route::post('/auth/email/send-otp', [EmailVerificationController::class, 'sendOtp']);
Route::post('/auth/email/verify-otp', [EmailVerificationController::class, 'verifyOtp']);

Route::post('/register', [AuthController::class, 'register']);

Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);
