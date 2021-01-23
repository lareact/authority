<?php


namespace Golly\Authority\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

/**
 * Class InputUserMiddleware
 * @package Golly\Authority\Http\Middleware
 */
class InputUserMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $request->json()->add(['user_id' => $request->user()->id]);
        }

        return $next($request);
    }
}
