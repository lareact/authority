<?php


namespace Golly\Authority\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiException
 * @package Golly\Authority\Exceptions
 */
class ApiException extends HttpException
{

    /**
     * @var string
     */
    protected $errorCode = '400000';


    /**
     * @var string
     */
    protected $errorMessage = '未定义的PAI错误';


    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return $this
     */
    public function setErrorCode(string $errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

}
