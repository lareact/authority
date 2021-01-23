<?php


namespace Golly\Authority\Models\Traits;

use Golly\Authority\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait HasRoles
 * @package Golly\Authority\Models\Traits
 * @method Builder role(string $role)
 */
trait HasRoles
{
    use HasPermissions;

    /**
     * @return void
     */
    public static function bootHasRoles()
    {
        static::deleting(function ($model) {
            if (
                method_exists($model, 'isForceDeleting')
                && !$model->isForceDeleting()
            ) {
                return;
            }

            $model->roles()->detach();
        });
    }

    /**
     * @param Builder $query
     * @param $roles
     * @return Builder
     */
    public function scopeRole(Builder $query, $roles): Builder
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) {
            $role = $this->findRole($role);
            if ($role instanceof Role) {
                return $role;
            }
            return null;
        }, $roles);

        return $query->whereHas('roles', function (Builder $subQuery) use ($roles) {
            $subQuery->whereIn('roles.id', array_column($roles, 'id'));
        });
    }


    /**
     * @return array
     */
    public function pluckRoles(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRoles(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (empty($role)) {
                    return false;
                }
                return $this->findRole($role);
            })->filter(function ($role) {
                return $role instanceof Role;
            })->pluck('id');

        if ($this->exists && $roles->isNotEmpty()) {
            $this->roles()->sync($roles->all(), false);
            $this->load('roles');
        }

        return $this;
    }

    /**
     * @param $role
     * @return $this
     */
    public function removeRole($role)
    {
        $role = $this->findRole($role);
        if ($role instanceof Role) {
            $this->roles()->detach($role->id);
            $this->load('roles');
        }

        return $this;
    }

    /**
     * @param mixed ...$roles
     * @return HasRoles
     */
    public function syncRoles(...$roles)
    {
        $this->roles()->detach();

        return $this->assignRoles($roles);
    }

    /**
     * @param $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles) && str_contains($roles, '|')) {
            $roles = explode('|', $roles);
        }
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }
        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }
        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }
        if ($roles instanceof Collection) {
            return $roles->isNotEmpty();
        }

        return false;
    }

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAnyRole(...$roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Return all permissions directly coupled to the model.
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @param $role
     * @return Role|string|null
     */
    protected function findRole($role)
    {
        if (is_numeric($role)) {
            return Role::findById($role);
        }
        if (is_string($role)) {
            return Role::findByName($role);
        }

        return $role;
    }

}
