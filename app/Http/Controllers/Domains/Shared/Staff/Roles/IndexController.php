<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Roles;

use Domains\Shared\Models\Role;
use Domains\Shared\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $roles = QueryBuilder::for(Role::class)
            ->allowedFilters([])
            ->allowedIncludes('permissions')
            ->get();

        return response(
            content: [
                'message' => 'Roles fetched successfully.',
                'roles' => RoleResource::collection(
                    resource: $roles,
                ),
            ],
            status: Http::OK(),
        );
    }
}
