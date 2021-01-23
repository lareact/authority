<?php


namespace Golly\Authority\Actions;


use Golly\Authority\Exceptions\ValidationException;
use Golly\Authority\Models\User;
use Illuminate\Http\Request;

/**
 * Class LoginAction
 * @package Golly\Authority\Actions
 */
class LoginAction extends Action
{

    /**
     * @var string
     */
    protected $errorMessage = '用户名或密码错误';

    /**
     * @param  $request
     * @return array
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $user = (new User())->where([
            'email' => $request->json('username')
        ])->first();
        if (!$user || !$user->checkPassword($request->json('password'))) {
            $this->failedValidation();
        }
        if (!$user->canLogin()) {
            $this->errorMessage = '当前用户被限制登陆';
            $this->failedValidation();
        }
        $expiration = config('authority.expiration');
        if ($user->is_tmp) {
            $expiration = $user->getExpirationMinutes();
        }

        $name = $request->getClientIp();
        $permissions = $user->pluckAllPermissions();
        $token = $user->createToken($name, $permissions, $expiration);

        return [
            'user' => $user->asArray(),
            'access_token' => $token->getBearerToken(),
            'expires_in' => $expiration,
            'roles' => $user->pluckRoles(),
            'permissions' => $permissions
        ];
    }
}
