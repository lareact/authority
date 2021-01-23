<?php


namespace Golly\Authority;


/**
 * Class Authority
 * @package Golly\Authority
 */
class Authority
{
    /**
     * Indicates if Authority routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Get the username used for authentication.
     *
     * @return string
     */
    public static function username()
    {
        return config('authority.username', 'name');
    }

    /**
     * Get the name of the email address request variable / field.
     *
     * @return string
     */
    public static function email()
    {
        return config('authority.email', 'email');
    }
}
