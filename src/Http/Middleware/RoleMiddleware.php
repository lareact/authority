<?php


namespace Golly\Authority\Http\Middleware;


use Closure;
use Golly\Authority\Exceptions\AccessDeniedHttpException;
use Golly\Authority\Exceptions\UnauthorizedException;
use Golly\Authority\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoleMiddleware
 * @package Golly\Authority\Http\Middleware
 */
class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            throw new UnauthorizedException();
        }
        /** @var User $user */
        $user = Auth::guard($guard)->user();
        $roles = is_array($role) ? $role : explode('|', $role);

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        throw AccessDeniedHttpException::forRole();
    }
}
