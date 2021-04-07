<?php


namespace Golly\Authority\Models;


use Carbon\Carbon;
use Database\Factories\UserFactory;
use Golly\Authority\Eloquent\Model;
use Golly\Authority\Filters\UserFilter;
use Golly\Authority\Models\Traits\HasAccessToken;
use Golly\Authority\Models\Traits\HasRoles;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 * @package Golly\Authority\Models
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $group
 * @property string|null $title
 * @property string|null $signature
 * @property string|null $avatar
 * @property string $password
 * @property boolean $is_admin
 * @property boolean $is_active
 * @property string $inactive_reason
 * @property boolean $is_tmp
 * @property string|null $tmp_started_at
 * @property string|null $tmp_ended_at
 * @property string|null $api_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * @property Collection $children
 * @property User $parent
 * @property Collection $roles
 * @property Collection $permissions
 */
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        MustVerifyEmail,
        Notifiable,
        HasAccessToken,
        HasRoles,
        HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'group',
        'title',
        'signature',
        'avatar',
        'is_admin',
        'is_active',
        'inactive_reason',
        'is_tmp',
        'tmp_started_at',
        'tmp_ended_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'is_tmp' => 'boolean'
    ];

    /**
     * @return UserFilter
     */
    public function newModelFilter()
    {
        return new UserFilter();
    }

    /**
     * @return HasMany
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(
            UserAccessToken::class,
            'user_id',
            'id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'user_has_roles',
            'user_id',
            'role_id',
            'id',
            'id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'user_has_permissions',
            'user_id',
            'permission_id',
            'id',
            'id'
        );
    }

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    /**
     * @return bool
     */
    public function canLogin(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->is_tmp ? $this->isTmpAllow() : true;
    }

    /**
     * @return false
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @return bool
     */
    public function isTmpAllow(): bool
    {
        if (
            $this->tmp_started_at &&
            $this->tmp_ended_at &&
            now()->gte($this->tmp_started_at) &&
            now()->lt($this->tmp_ended_at)
        ) {
            return true;
        }

        return false;
    }

    /**
     * 获取账号过期时间(分钟)
     *
     * @return int
     */
    public function getExpirationMinutes(): int
    {
        if ($this->isTmpAllow()) {
            return now()->diffInMinutes($this->tmp_ended_at);
        }

        return 0;
    }

    /**
     * @return UserFactory
     */
    protected static function newFactory()
    {
        return new UserFactory();
    }
}
