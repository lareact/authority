<?php


namespace Golly\Authority\Http\Requests;

/**
 * Class VerifyEmailRequest
 * @package Golly\Authority\Http\Requests
 */
class VerifyEmailRequest extends ApiRequest
{

    /**
     * @var string
     */
    protected $errorMessage = '邮件认证错误';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->isEqualId() || !$this->isEqualHash()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isEqualId()
    {
        return hash_equals((string)$this->route('id'), (string)$this->user()->getKey());
    }

    /**
     * @return bool
     */
    protected function isEqualHash()
    {
        return hash_equals((string)$this->route('hash'), sha1($this->user()->getEmailForVerification()));
    }
}
