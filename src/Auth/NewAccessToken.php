<?php


namespace Golly\Authority\Auth;


use Golly\Authority\Models\UserAccessToken;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class NewAccessToken
 * @package Golly\Authority
 */
class NewAccessToken implements Arrayable, Jsonable
{
    /**
     * The access token instance.
     *
     * @var UserAccessToken
     */
    public $accessToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * NewAccessToken constructor.
     * @param UserAccessToken $accessToken
     * @param string $plainTextToken
     */
    public function __construct(
        UserAccessToken $accessToken,
        string $plainTextToken
    )
    {
        $this->accessToken = $accessToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getBearerToken($type = 'Bearer')
    {
        return $type . ' ' . $this->accessToken->id . '|' . $this->plainTextToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'accessToken' => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
