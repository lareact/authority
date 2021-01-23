<?php


namespace Golly\Authority\Http\Middleware;


use Closure;
use Golly\Authority\Exceptions\AccessDeniedHttpException;
use Golly\Authority\Exceptions\UnauthorizedException;
use Golly\Authority\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionMiddleware
 * @package Golly\Authority\Http\Middleware
 */
class PermissionMiddleware
{

    /**
     * @param $request
     * @param Closure $next
     * @param $permission
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            throw new UnauthorizedException();
        }
        $permissions = is_array($permission) ? $permission : explode('|', $permission);
        /** @var User $user */
        $user = Auth::guard($guard)->user();
        if ($user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        throw AccessDeniedHttpException::forPermission();
    }
}
