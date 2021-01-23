<?php


namespace Golly\Authority\Models;


use Golly\Authority\Eloquent\Model;
use Golly\Authority\Models\Filters\RoleFilter;
use Golly\Authority\Models\Traits\HasPermissions;
use Golly\Authority\Models\Traits\RefreshPermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 * @package Golly\Authority\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Collection $permissions
 */
class Role extends Model
{
    use HasPermissions,
        RefreshPermissions;

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
     * @return RoleFilter
     */
    public function newModelFilter()
    {
        return new RoleFilter();
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_has_roles',
            'role_id',
            'user_id',
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
            'role_has_permissions',
            'role_id',
            'permission_id',
            'id',
            'id'
        );
    }

    /**
     * @param $id
     * @return Role|null
     */
    public static function findById($id)
    {
        return (new static())->where('id', $id)->first();
    }

    /**
     * @param $name
     * @return Role|null
     */
    public static function findByName($name)
    {
        return (new static())->where('name', $name)->first();
    }

}
