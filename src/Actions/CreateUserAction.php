<?php


namespace Golly\Authority\Actions;

use Golly\Authority\Exceptions\ValidationException;
use Golly\Authority\Models\User;
use Golly\Authority\Traits\HasPasswordRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\ParameterBag;
use Throwable;

/**
 * Class CreateUserAction
 * @package Golly\Authority\Actions
 */
class CreateUserAction extends Action
{
    use HasPasswordRules;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'parent_id' => 'sometimes|integer',
            'name' => 'required|string|max:36',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits:11',
            'group' => 'string',
            'password' => $this->getPasswordRules(),
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '用户名',
            'email' => '邮箱'
        ];
    }

    /**
     * @param ParameterBag $json
     * @return User
     * @throws ValidationException
     */
    public function create(ParameterBag $json)
    {
        if ($id = $json->get('parent_id')) {
            $user = (new User())->find($id);
            if (!$user || !$user->isParent()) {
                $this->addError('parent_id', '父账号不存在。');
            }
        }
        $data = $this->validate($json->all());
        try {
            $user = (new User())->create(array_merge($data, [
                'password' => Hash::make($json->get('password'))
            ]));
            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
            }

            return $user;
        } catch (Throwable $e) {
            $this->failedValidation();
        }
    }
}
