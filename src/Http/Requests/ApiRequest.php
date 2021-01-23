<?php


namespace Golly\Authority\Http\Requests;

use Golly\Authority\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ApiRequest
 * @package Golly\Authority\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{

    /**
     * @var string
     */
    protected $errorMessage = 'API请求验证错误';

    /**
     * @return array
     */
    abstract public function rules();

    /**
     * 如果有特殊需求，可自己处理
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * 重新定义异常信息，添加errorCode
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))->setMessage($this->errorMessage);
    }
}
