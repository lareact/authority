<?php


namespace Golly\Authority\Actions;

use Golly\Authority\Exceptions\ValidationException;
use Golly\Authority\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class UpdateUserAction
 * @package Golly\Authority\Actions
 */
class UpdateUserAction extends Action
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:36',
            'phone' => 'required|digits:11',
            'is_active' => 'required|boolean',
            'email' => [
                'required',
                Rule::unique('users')->ignore($this->id)
            ],
        ];
    }


    /**
     * 在表单请求后添加钩子
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->sometimes('inactive_reason', 'required|max:255', function (Fluent $input) {
            return !$input->get('is_active', true);
        });
    }

    /**
     * @param $id
     * @param ParameterBag $input
     * @return User
     * @throws ValidationException
     */
    public function update($id, ParameterBag $input)
    {
        $this->id = $id;
        $user = (new User())->findOrFail($id);
        $data = $this->validate($input->all());
        $user->fill($data)->save();

        return $user;
    }
}
