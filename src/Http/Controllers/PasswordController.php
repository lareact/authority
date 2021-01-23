<?php


namespace Golly\Authority\Http\Controllers;

use Golly\Authority\Exceptions\PasswordException;
use Golly\Authority\Models\User;
use Golly\Authority\Traits\HasPasswordRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * Class PasswordController
 * @package Golly\Authority\Http\Controllers
 */
class PasswordController extends ApiController
{
    use HasPasswordRules;

    /**
     * 忘记密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        $status = Password::broker()->sendResetLink($request->only(['email']));
        if ($status == Password::RESET_LINK_SENT) {
            return $this->sendMessage(trans($status));
        } else {
            throw PasswordException::forAny(trans($status));
        }
    }

    /**
     * 重置密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => $this->getPasswordRules()
        ]);

        $status = Password::broker()->reset(
            $request->only(['email', 'token', 'password']),
            function (User $user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return $this->sendMessage(trans($status));
        } else {
            throw PasswordException::forAny(trans('passwords.reset_failed'));
        }
    }
}
