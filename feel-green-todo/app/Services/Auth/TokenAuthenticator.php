<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\MissingAuthenticationException;
use App\Exceptions\ValidationException;
use App\Services\Security\Throttle\LoginRateLimiter;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Token Authenticator
 *
 * @copyright Elena Cretul
 */
class TokenAuthenticator
{
    /**
     * Constructor
     *
     * @param Guard $auth
     * @param LoginRateLimiter $loginRateLimiter
     */
    public function __construct(
        private readonly Guard $auth,
        private readonly LoginRateLimiter $loginRateLimiter,
    ){
    }

    /**
     * Authenticate
     *
     * @param array $credentials
     * @param Request $request
     *
     * @return string
     *
     * @throws MissingAuthenticationException
     * @throws ValidationException
     */
    public function authenticate(array $credentials, Request $request): string
    {
        $this->ensureIsNotRateLimited($request);
        $this->attemptLogin($credentials, $request);

        return $this->generateToken();
    }

    /**
     * Ensure is not rate limited
     *
     * @param Request $request
     *
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if ($this->loginRateLimiter->tooManyAttempts($request)) {
            event(new Lockout($request));

            throw new ValidationException(sprintf(
                "Too many attempts. Please try again in %d seconds.",
                $this->loginRateLimiter->availableIn($request),
            ));
        }
    }

    /**
     * @throws MissingAuthenticationException
     */
    private function attemptLogin(array $credentials, Request $request): void
    {
        if (!$this->auth->attempt($credentials)) {
            $this->loginRateLimiter->increment($request);

            throw new MissingAuthenticationException('User not found for the credentials provided.');
        }

        $this->loginRateLimiter->clear($request);
    }

    /**
     * @throws MissingAuthenticationException
     */
    private function generateToken(): string
    {
        $user = $this->auth->user() ?? throw new MissingAuthenticationException('Authenticated user not found.');

        return $user->createToken('auth_token')->plainTextToken;
    }
}
