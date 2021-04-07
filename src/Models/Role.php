<?php


namespace Golly\Authority\Models;


use Golly\Authority\Eloquent\Model;
use Golly\Authority\Filters\NameFilter;
use Golly\Authority\Models\Traits\HasPermissions;
use Golly\Authority\Models\Traits\RefreshPermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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
     * @return NameFilter
     */
    public function newModelFilter()
    {
        return new NameFilter();
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
     * @param array $roles
     * @return Collection
     */
    public static function findRoles(array $roles)
    {
        $ids = [];
        $names = [];
        foreach ($roles as $role) {
            if (is_numeric($role)) {
                $ids[] = $role;
            } else {
                $names[] = $role;
            }
        }
        $query = self::query();
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        if ($names) {
            $query->orWhereIn('name', $names);
        }

        return $query->get();
    }
}
