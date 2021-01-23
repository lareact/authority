<?php


namespace Golly\Authority\Http\Middleware;


use Closure;
use Golly\Authority\Events\LockoutEvent;
use Golly\Authority\Exceptions\RateLimiterException;
use Golly\Authority\RequestRateLimiter;
use Illuminate\Http\Request;

/**
 * Class RateLimiterMiddleware
 * @package Golly\Authority\Http\Middleware
 */
class RateLimiterMiddleware
{
    /**
     * The login rate limiter instance.
     *
     * @var RequestRateLimiter
     */
    protected $limiter;

    /**
     * RateLimiterMiddleware constructor.
     * @param RequestRateLimiter $limiter
     */
    public function __construct(RequestRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int $maxAttempts
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 5)
    {
        if (!$this->limiter->tooManyAttempts($request, $maxAttempts)) {
            return $next($request);
        }

        event(new LockoutEvent($request));
        $seconds = $this->limiter->availableIn($request);

        throw (new RateLimiterException())->setErrorMessage(
            sprintf('请勿频繁请求，%s秒后可重试', $seconds)
        );
    }

}
