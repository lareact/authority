<?php


namespace Golly\Authority\Services;


use Closure;
use Golly\Authority\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\DB;

/**
 * Class LibService
 * @package Golly\Authority\Services
 */
class LibService
{
    use Dispatchable;

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
     * @return User|Authenticatable|null
     */
    protected function user()
    {
        return auth()->user();
    }
}
