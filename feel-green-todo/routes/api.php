<?php

use App\Http\Controllers\Api\Auth\AuthenticationApiController;
use App\Http\Controllers\Api\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
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
        ->uses([AuthenticationApiController::class, 'store']);
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
        ->uses([AuthenticationApiController::class, 'destroy'])
        ->middleware('auth:sanctum');
});
