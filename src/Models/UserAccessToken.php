<?php


namespace Golly\Authority\Models;


use Golly\Authority\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserAccessToken
 * @package Golly\Authority\Models
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $token
 * @property array $abilities
 * @property string $expired_at
 * @property User $user
 */
class UserAccessToken extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'expired_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'abilities' => 'json',
        'expired_at' => 'datetime',
        'last_used_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param string $token
     * @return UserAccessToken|null
     */
    public static function findToken(string $token)
    {
        $instance = (new static());
        if (strpos($token, '|') === false) {
            return $instance->where('token', hash('sha256', $token))->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = $instance->find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->expired_at ? now()->gt($this->expired_at) : true;
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param string $ability
     * @return bool
     */
    public function can(string $ability): bool
    {
        return in_array('*', $this->abilities) ||
            array_key_exists($ability, array_flip($this->abilities));
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param string $ability
     * @return bool
     */
    public function cant(string $ability): bool
    {
        return !$this->can($ability);
    }
}
