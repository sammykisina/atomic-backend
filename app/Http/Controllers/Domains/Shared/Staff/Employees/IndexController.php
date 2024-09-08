<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Employees;

use Domains\Shared\Models\User;
use Domains\Shared\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $staff  = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::scope('type'),
            ])
            ->allowedIncludes('department', 'region', 'role.permissions')
            ->get();

        return response(
            content: [
                'message' => 'Staff fetched successfully.',
                'staff' => UserResource::collection(
                    resource: $staff,
                ),
            ],
            status: Http::OK(),
        );
    }
}
