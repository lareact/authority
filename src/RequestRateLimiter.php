<?php


namespace Golly\Authority;


use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class RequestRateLimiter
 * @package Golly\Authority
 */
class RequestRateLimiter
{
    /**
     * The login rate limiter instance.
     *
     * @var RateLimiter
     */
    protected $limiter;

    /**
     * Create a new login rate limiter instance.
     *
     * @param RateLimiter $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param Request $request
     * @return mixed
     */
    public function attempts(Request $request)
    {
        return $this->limiter->attempts($this->throttleKey($request));
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param Request $request
     * @param int $maxAttempts
     * @return bool
     */
    public function tooManyAttempts(Request $request, $maxAttempts = 5)
    {
        $attempts = $this->limiter->tooManyAttempts(
            $this->throttleKey($request),
            $maxAttempts
        );
        if (!$attempts) {
            $this->increment($request);
        }

        return $attempts;
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param Request $request
     * @return void
     */
    public function increment(Request $request)
    {
        $this->limiter->hit($this->throttleKey($request), 60);
    }

    /**
     * Determine the number of seconds until logging in is available again.
     *
     * @param Request $request
     * @return int
     */
    public function availableIn(Request $request)
    {
        return $this->limiter->availableIn($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param Request $request
     * @return void
     */
    public function clear(Request $request)
    {
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->path()) . '|' . $request->ip();
    }
}
