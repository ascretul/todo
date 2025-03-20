<?php

declare(strict_types=1);

namespace App\Services\Security\Throttle;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Login RateLimiter.
 *
 * Custom implementation of Login RateLimiter from Fortify package with few adjustments.
 *
 * @link https://github.com/laravel/fortify/blob/1.x/src/LoginRateLimiter.php
 *
 * @copyright Elena Cretul
 *
 * @todo Rename methods into more informative names
 */
class LoginRateLimiter
{
    private const MAXIMUM_ATTEMPTS = 5;
    private const DECAY_MINUTES = 15;
    private const USERNAME_KEY = 'username';

    /**
     * Constructor.
     *
     * @param RateLimiter $limiter
     */
    public function __construct(protected readonly RateLimiter $limiter)
    {
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param Request $request
     *
     * @return int
     */
    public function attempts(Request $request): int
    {
        return (int)$this->limiter->attempts($this->throttleKey($request));
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function tooManyAttempts(Request $request): bool
    {
        return $this->limiter->tooManyAttempts($this->throttleKey($request), static::MAXIMUM_ATTEMPTS);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param Request $request
     */
    public function increment(Request $request): void
    {
        $this->limiter->hit($this->throttleKey($request), static::DECAY_MINUTES);
    }

    /**
     * Determine the number of seconds until logging in is available again.
     *
     * @param Request $request
     *
     * @return int
     */
    public function availableIn(Request $request): int
    {
        return $this->limiter->availableIn($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param Request $request
     */
    public function clear(Request $request): void
    {
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input(static::USERNAME_KEY)) . '|' . $request->ip();
    }
}
