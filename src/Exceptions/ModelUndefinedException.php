<?php


namespace Golly\Authority\Exceptions;


use Throwable;

/**
 * Class ModelUndefinedException
 * @package Golly\Authority\Exceptions
 */
class ModelUndefinedException extends ApiException
{
    /**
     * @var string
     */
    protected $errorCode = '500000';


    /**
     * @var string
     */
    protected $errorMessage = '当前Model没有被定义';

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
        parent::__construct(500, $message, $previous, $headers, $code);
    }
}
