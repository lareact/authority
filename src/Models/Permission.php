<?php


namespace Golly\Authority\Models;


use Golly\Authority\Eloquent\Model;
use Golly\Authority\Filters\NameFilter;
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
     * @param array $permissions
     * @return Collection
     */
    public static function findPermissions(array $permissions)
    {
        $ids = [];
        $names = [];
        foreach ($permissions as $permission) {
            if (is_numeric($permission)) {
                $ids[] = $permission;
            } else {
                $names[] = $permission;
            }
        }
        $self = new static;
        if ($ids) {
            $self->whereIn('id', $ids);
        }
        if ($names) {
            $self->orWhereIn('name', $names);
        }

        return $query->get();
    }

    /**
     * @param int $id
     * @return Permission|null
     */
    public static function findById(int $id)
    {
        return (new static)->where('id', $id)->first();
    }

    /**
     * @param string $name
     * @return Permission|null
     */
    public static function findByName(string $name)
    {
        return (new static)->where('name', $name)->first();
    }
}
