<?php


namespace Golly\Authority\Exceptions;


use Illuminate\Validation\ValidationException as LaravelValidationException;

/**
 * Class ValidationException
 * @package Golly\Authority\Exceptions
 */
class ValidationException extends LaravelValidationException
{

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

}
