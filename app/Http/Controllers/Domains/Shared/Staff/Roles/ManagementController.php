<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Roles;

use Domains\Shared\Models\Permission;
use Domains\Shared\Models\Role;
use Domains\Shared\Models\User;
use Domains\Shared\Requests\RevokeAbilityRequest;
use Domains\Shared\Requests\Staff\CreateOrEditRoleRequest;
use Domains\Shared\Requests\Staff\PermissionsRequest;
use Domains\Shared\Resources\RoleResource;
use Domains\Shared\Services\Staff\RoleService;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected RoleService $roleService,
    ) {}


    /**
     * CREATE ROLE
     * @param CreateOrEditRoleRequest $request
     * @return Response|HttpException
     */
    public function create(CreateOrEditRoleRequest $request): Response | HttpException
    {
        $role = $this->roleService->createRole(
            roleData: $request->validated(),
        );

        if ( ! $role) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Role creation failed.',
            );
        }

        return response(
            content: [
                'role' => new RoleResource(
                    resource: $role,
                ),
                'message' => 'Role created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT ROLE
     * @param CreateOrEditRoleRequest $request
     * @param Role $role
     * @return Response | HttpException
     */
    public function edit(CreateOrEditRoleRequest $request, Role $role): Response | HttpException
    {
        if ( ! $this->roleService->editRole(
            role: $role,
            updatedRoleData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Role creation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Role updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CREATE PERMISSIONS
     * @param PermissionsRequest $request
     * @param Role $role
     * @return Response|HttpException
     */
    public function createPermissions(PermissionsRequest $request, Role $role): Response | HttpException
    {
        $permissions = $this->roleService->createPermissions(
            role: $role,
            abilities: $request->validated(),
        );

        if (0 === $permissions->count()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Permissions creation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Permissions created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * REVOKE ROLE PERMISSIONS
     * @param Permission $permission
     * @return Response|HttpException
     */
    public function revokePermission(Permission $permission): Response | HttpException
    {
        if ( ! $this->roleService->deletePermission(
            permission: $permission,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Permissions revoking failed.',
            );
        }

        return response(
            content: [
                'message' => 'Permission revoked successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * REVOKE ABILITY
     * @param RevokeAbilityRequest $request
     * @param Permission $permission
     * @return void
     */
    public function revokeAbility(RevokeAbilityRequest $request, Permission $permission): Response | HttpException
    {
        if ( ! $this->roleService->deleteAbility(
            permission: $permission,
            ability: $request->validated()['ability'],
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Permission ability revoking failed.',
            );
        }

        return response(
            content: [
                'message' => 'Permission ability revoked successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ASSIGN ROLE TO USER
     * @param Role $role
     * @param User $user
     * @return Response|HttpException
     */
    public function assignUserRole(Role $role, User $user): Response | HttpException
    {
        if ( ! $user->is_admin && null !== $user->role_id) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This user has already an assign role. Please revoke the current role to assign a new one',
            );
        }

        if ( ! $user->update([
            'role_id' => $role->id,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'User role assigning failed.',
            );
        }

        return response(
            content: [
                'message' => 'Role assigned successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
