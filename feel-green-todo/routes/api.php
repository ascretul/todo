<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('/', static function (Request $request) {return $request->user();})
        ->middleware(['auth:sanctum']);
});
Route::prefix('auth')->group(function () {
    Route::post('/register')
        ->uses([RegisteredUserController::class, 'store']);
    Route::post('/login')
        ->uses([AuthenticationController::class, 'store']);
    Route::post('/forgot-password')
        ->uses([PasswordResetLinkController::class, 'store']);
    Route::post('/reset-password')
        ->uses([NewPasswordController::class, 'store']);
    Route::get('/verify-email/{id}/{hash}')
        ->uses(VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification')
        ->uses([EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:6,1']);
    Route::post('/logout')
        ->uses([AuthenticationController::class, 'destroy'])
        ->middleware('auth:sanctum');
});
