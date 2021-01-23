<?php


namespace Golly\Authority\Exceptions;

use Throwable;

/**
 * Class UnauthorizedException
 * @package Golly\Authority\Exceptions
 */
class UnauthorizedException extends ApiException
{
    /**
     * @var string
     */
    protected $errorCode = '401001';

    /**
     * @var string
     */
    protected $errorMessage = '出现认证错误，请先登陆系统';

    /**
     * UnauthorizedException constructor.
     * @param string|null $message
     * @param Throwable|null $previous
     * @param int|null $code
     * @param array $headers
     */
    public function __construct(
        string $message = null,
        Throwable $previous = null,
        ?int $code = 0,
        array $headers = [])
    {
        parent::__construct(401, $message, $previous, $headers, $code);
    }

}
