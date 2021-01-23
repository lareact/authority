<?php


namespace Golly\Authority\Exceptions;

use Throwable;

/**
 * Class PasswordException
 * @package Golly\Authority\Exceptions
 */
class PasswordException extends ApiException
{

    /**
     * @var string
     */
    protected $errorCode = '422010';


    /**
     * @var string
     */
    protected $errorMessage = '密码相关操作错误';

    /**
     * PasswordException constructor.
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
        parent::__construct(422, $message, $previous, $headers, $code);
    }

    /**
     * @param string $message
     * @return PasswordException
     */
    public static function forAny(string $message)
    {
        return (new static())->setErrorMessage($message);
    }

}
