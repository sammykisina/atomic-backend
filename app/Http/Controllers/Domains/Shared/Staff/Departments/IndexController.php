<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Domains\Shared\Models\Department;
use Domains\Shared\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $departments = QueryBuilder::for(Department::class)
            ->allowedFilters([])
            ->get();

        return response(
            content: [
                'message' => 'departments fetched successfully.',
                'departments' => DepartmentResource::collection(
                    resource: $departments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
