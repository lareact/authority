<?php


namespace Golly\Authority\Models;


use Golly\Authority\Eloquent\Model;
use Golly\Authority\Models\Filters\PermissionFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Permission
 * @package Golly\Authority\Models
 * @property string $name
 * @property string $description
 * @property Collection $roles
 */
class Permission extends Model
{

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'description'
    ];

    /**
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * @return PermissionFilter
     */
    public function newModelFilter()
    {
        return new PermissionFilter();
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_has_permissions',
            'permission_id',
            'user_id',
            'id',
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
            'role_has_permissions',
            'permission_id',
            'role_id',
            'id',
            'id'
        );
    }

    /**
     * @param $id
     * @return Permission|null
     */
    public static function findById($id)
    {
        return (new static())->where('id', $id)->first();
    }

    /**
     * @param $name
     * @return Permission|null
     */
    public static function findByName($name)
    {
        return (new static())->where('name', $name)->first();
    }
}
