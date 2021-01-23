<?php


namespace Golly\Authority\Models\Traits;

use Golly\Authority\Models\Permission;
use Golly\Authority\Models\Role;
use Golly\Authority\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait HasPermissions
 * @package Golly\Authority\Models\Traits
 * @method Builder permission(string $permission)
 */
trait HasPermissions
{

    /**
     * @return void
     */
    public static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            if (
                method_exists($model, 'isForceDeleting')
                && !$model->isForceDeleting()
            ) {
                return;
            }

            $model->permissions()->detach();
        });
    }

    /**
     * @param Builder $query
     * @param $permissions
     * @return Builder
     */
    public function scopePermission(Builder $query, $permissions): Builder
    {
        $permissions = $this->toPermissionModels($permissions);
        $roles = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->where(function (Builder $query) use ($permissions, $roles) {
            $query->whereHas('permissions', function (Builder $subQuery) use ($permissions) {
                $subQuery->whereIn('permissions.id', array_column($permissions, 'id'));
            });
            if (count($roles) > 0) {
                $query->orWhereHas('roles', function (Builder $subQuery) use ($roles) {
                    $subQuery->whereIn('roles.id', array_column($roles, 'id'));
                });
            }
        });
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasPermission(string $name): bool
    {
        $permission = $this->findPermission($name);
        if (!$permission instanceof Permission) {
            return false;
        }

        return $this->hasDirectPermission($permission) ||
            $this->hasPermissionViaRole($permission);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasDirectPermission($permission): bool
    {
        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * @param Permission $permission
     * @return bool
     */
    public function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function checkPermission($permission): bool
    {
        return $this->hasPermission($permission);
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasAnyPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->checkPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     */
    public function hasAllPermissions(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param mixed ...$permissions
     * @return $this
     */
    public function assignPermissions(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->findPermission($permission);
            })->filter(function ($permission) {
                return $permission instanceof Permission;
            })->pluck('id');
        if ($this->exists && $permissions->isNotEmpty()) {
            $this->permissions()->sync($permissions->all(), false);
            $this->load('permissions');
        }

        return $this;
    }

    /**
     * Remove all current permissions and set the given ones.
     *
     * @param mixed ...$permissions
     * @return Role|User
     */
    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->assignPermissions($permissions);
    }

    /**
     * Return all the permissions the model has via roles.
     *
     * @return Collection
     */
    public function getPermissionsViaRoles(): Collection
    {
        return $this->loadMissing(['roles.permissions'])
            ->roles
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->sort()
            ->values();
    }

    /**
     * Return all the permissions the model has, both directly and via roles.
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        // direct permissions
        $permissions = $this->permissions;
        if ($this->roles) {
            $permissions = $permissions->merge($this->getPermissionsViaRoles());
        }

        return $permissions->sort()->values();
    }


    /**
     * @return array
     */
    public function pluckAllPermissions(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * @param $permissions
     * @return array
     */
    protected function toPermissionModels($permissions): array
    {
        if ($permissions instanceof Collection) {
            $permissions = $permissions->all();
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];

        return array_map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission;
            }
            return $this->findPermission($permission);
        }, $permissions);
    }

    /**
     * @param $permission
     * @return Permission|Collection|string|null
     */
    protected function findPermission($permission)
    {
        if (is_numeric($permission)) {
            return Permission::findById($permission);
        }
        if (is_string($permission)) {
            return Permission::findByName($permission);
        }
        if (is_array($permission)) {
            return (new Permission())->whereIn('name', $permission)->get();
        }

        return $permission;
    }
}
