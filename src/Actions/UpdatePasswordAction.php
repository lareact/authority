<?php


namespace Golly\Authority\Actions;


use Golly\Authority\Exceptions\ValidationException;
use Golly\Authority\Models\User;
use Golly\Authority\Traits\HasPasswordRules;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class UpdatePasswordAction
 * @package Golly\Authority\Actions
 */
class UpdatePasswordAction extends Action
{
    use HasPasswordRules;


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => 'required|string',
            'new_password' => $this->getPasswordRules()
        ];
    }

    /**
     * @param User $user
     * @param ParameterBag $input
     * @return User
     * @throws ValidationException
     */
    public function update(User $user, ParameterBag $input)
    {
        if ($user->checkPassword($input->get('old_password'))) {
            $this->addError('old_password', '原始密码错误！');
        }
        $this->validate($input->all());

        $user->forceFill([
            'password' => Hash::make($input->get('new_password'))
        ])->save();

        return $user;
    }
}
