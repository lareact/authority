<?php


namespace Golly\Authority\Models\Traits;


use Golly\Authority\Auth\NewAccessToken;
use Golly\Authority\Models\UserAccessToken;
use Illuminate\Support\Str;

/**
 * Trait HasAccessToken
 * @package Golly\Authority\Models\Traits
 */
trait HasAccessToken
{
    /**
     * @var UserAccessToken
     */
    protected $accessToken;

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array|string[] $abilities
     * @param int|null $expiration
     * @return NewAccessToken
     */
    public function createToken(
        string $name,
        array $abilities = ['*'],
        int $expiration = null
    )
    {
        $plainTextToken = Str::random(64);
        /** @var UserAccessToken $token */
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expired_at' => $expiration ? now()->addMinutes($expiration) : null
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     * @return bool
     */
    public function tokenCan(string $ability): bool
    {
        return $this->accessToken ? $this->accessToken->can($ability) : false;
    }

    /**
     * @return UserAccessToken
     */
    public function getAccessToken(): UserAccessToken
    {
        return $this->accessToken;
    }

    /**
     * @param UserAccessToken $accessToken
     * @return $this
     */
    public function setAccessToken(UserAccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

}
