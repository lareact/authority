<?php


namespace Golly\Authority\Services;


use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class LibService
 * @package Golly\Authority\Services
 */
class LibService
{
    use AuthorizesRequests, Dispatchable;

    /**
     * @return Authenticatable|null
     */
    protected function user()
    {
        return auth()->user();
    }

    /**
     * @param Closure $callback
     * @param int $attempts
     * @return mixed
     */
    protected function transaction(Closure $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }


    /**
     * @param string $key
     * @param Carbon $ttl
     * @param Closure $callback
     * @return mixed
     */
    protected function remember(string $key, Carbon $ttl, Closure $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

}
