<?php


namespace Golly\Authority\Actions;


use Closure;
use Golly\Authority\Exceptions\ValidationException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Class Action
 * @package Golly\Authority\Actions
 */
class Action
{
    use Dispatchable;

    /**
     * @var string
     */
    protected $errorMessage = '存在无效的数据。';


    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param Closure $callback
     * @param int $attempts
     * @return mixed
     */
    public function transaction(Closure $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }

    /**
     * @param string $key
     * @param string $message
     * @return void
     */
    public function addError(string $key, string $message)
    {
        $this->errors[$key][] = $message;
    }

    /**
     * @param array $data
     * @param bool $safe
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data, $safe = false)
    {
        $validator = Validator::make(
            $data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }
        // 错误处理
        if ($validator->fails() || $this->errors) {
            $this->failedValidation($validator);
        }
        try {
            return $safe ? $validator->validated() : $data;
        } catch (Throwable $e) {
            $this->failedValidation($validator);
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation($validator = null)
    {
        if (is_null($validator)) {
            $validator = Validator::make([], []);
        }
        if ($this->errors) {
            foreach ($this->errors as $key => $messages) {
                foreach ($messages as $message) {
                    $validator->errors()->add($key, $message);
                }
            }
        }

        throw (new ValidationException($validator))->setMessage($this->errorMessage);
    }
}
