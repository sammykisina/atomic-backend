<?php

declare(strict_types=1);

namespace Domains\Shared\Services\Staff;

use Domains\Shared\Models\Permission;
use Domains\Shared\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class RoleService
{
    /**
     * GET ROLE BY ID
     * @param int $role_id
     * @return Role|null
     */
    public static function getRole(int $role_id): Role|null
    {
        return Role::query()->where('id', $role_id)->with('permissions')->first();
    }
    /**
     * CREATE ROLE
     * @param array $roleData
     * @return Role
     */
    public function createRole(array $roleData): Role
    {
        return Role::query()->create([
            'name' => $roleData['name'],
            'description' => $roleData['description'],
            'creator_id' => Auth::id(),
        ]);
    }

    /**
     * UPDATE ROLE
     * @param Role $role
     * @param array $updatedRoleData
     * @return bool
     */
    public function editRole(Role $role, array $updatedRoleData): bool
    {
        return $role->update([
            'name' => $updatedRoleData['name'],
            'description' => $updatedRoleData['description'],
            'updater_id' => Auth::id(),
        ]);
    }

    /**
     * CREATE PERMISSIONS
     * @param Role $role
     * @param array $abilities
     * @return Collection
     */
    public function createPermissions(Role $role, array $abilities): Collection
    {
        return collect($abilities['abilities'])->each(function ($ability) use ($role): void {
            Permission::updateOrCreate(
                ['role_id' => $role->id, 'module' => $ability['module']],
                ['abilities' =>  json_encode($ability['permissions'])],
            );
        });
    }

    /**
     * DELETE PERMISSION
     * @param Permission $permission
     * @return bool
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }

    /**
     * DELETE PERMISSION ABILITY
     * @param Permission $permission
     * @param string $ability
     * @return Permission
     */
    public function deleteAbility(Permission $permission, string $ability): Permission
    {
        return Permission::updateOrCreate(
            ['role_id' => $permission->role_id, 'module' => $permission->module],
            ['abilities' =>  json_encode(
                collect(json_decode($permission->abilities))->filter(fn(string $value) => $value !== $ability)->toArray(),
            )],
        );
    }
}
