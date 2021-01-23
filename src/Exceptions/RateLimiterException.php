<?php


namespace Golly\Authority\Exceptions;


use Throwable;

/**
 * Class RateLimiterException
 * @package Golly\Authority\Exceptions
 */
class RateLimiterException extends ApiException
{

    /**
     * @var string
     */
    protected $errorCode = '429000';

    /**
     * @var string
     */
    protected $errorMessage = '请勿频繁请求';

    /**
     * AccessDeniedHttpException constructor.
     *
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
        parent::__construct(429, $message, $previous, $headers, $code);
    }


}
