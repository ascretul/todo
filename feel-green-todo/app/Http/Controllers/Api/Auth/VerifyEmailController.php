<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash): JsonResponse
    {
//        dd(123);
//        if ($request->user()->hasVerifiedEmail()) {
//            return redirect()->intended(
//                config('app.frontend_url').'/dashboard?verified=1'
//            );
//        }
//
//        if ($request->user()->markEmailAsVerified()) {
//            event(new Verified($request->user()));
//        }
//
//        return redirect()->intended(
//            config('app.frontend_url').'/dashboard?verified=1'
//        );

        $user = User::findOrFail($id);

        // Проверяем, не верифицирован ли уже email
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Проверяем, соответствует ли хеш email пользователя
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        // Верифицируем email
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }
}
