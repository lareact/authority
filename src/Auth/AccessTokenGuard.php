<?php


namespace Golly\Authority\Auth;


use Golly\Authority\Models\Traits\HasAccessToken;
use Golly\Authority\Models\UserAccessToken;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * Class AccessTokenGuard
 * @package Golly\Authority\Auth
 */
class AccessTokenGuard implements Guard
{
    use GuardHelpers;

    protected $provider;

    /**
     * @var Request
     */
    protected $request;

    /**
     * AccessTokenGuard constructor.
     * @param UserProvider $provider
     * @param Request $request
     */
    public function __construct(
        UserProvider $provider,
        Request $request
    )
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }
        $user = null;
        if ($token = $this->request->bearerToken()) {
            $accessToken = UserAccessToken::findToken($token);
            if ($accessToken && $accessToken->isAvailable()) {
                $user = $accessToken->user->setAccessToken(
                    tap($accessToken->forceFill(['last_used_at' => now()]))->save()
                );
            }
        }

        return $this->user = $user;
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     * @return bool
     */
    protected function supportToken($tokenable = null)
    {
        return $tokenable &&
            in_array(HasAccessToken::class, class_uses_recursive(get_class($tokenable)));
    }
}
