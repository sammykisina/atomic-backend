<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Departments;

use Domains\Shared\Models\Department;
use Domains\Shared\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $departments = QueryBuilder::for(Department::class)
            ->allowedFilters([])
            ->get();

        return response(
            content: [
                'message' => 'Departments fetched successfully.',
                'departments' => DepartmentResource::collection(
                    resource: $departments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
