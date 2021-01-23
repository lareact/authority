<?php


namespace Golly\Authority\Exceptions;


use Throwable;

/**
 * Class AccessDeniedHttpException
 * @package Golly\Authority\Exceptions
 */
class AccessDeniedHttpException extends ApiException
{

    /**
     * AccessDeniedHttpException constructor.
     * @param string|null $message
     * @param Throwable|null $previous
     * @param int $code
     * @param array $headers
     */
    public function __construct(
        string $message = null,
        Throwable $previous = null,
        int $code = 0,
        array $headers = []
    )
    {
        parent::__construct(403, $message, $previous, $headers, $code);
    }

    /**
     * @return static
     */
    public static function forRole()
    {
        return (new static())->setErrorMessage(trans('auth.forbidden'));
    }

    /**
     * @return static
     */
    public static function forPermission()
    {
        return (new static())->setErrorMessage(trans('auth.forbidden'));
    }

}
