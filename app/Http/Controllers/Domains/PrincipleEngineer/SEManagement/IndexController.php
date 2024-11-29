<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PrincipleEngineer\SEManagement;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $userRegions  = QueryBuilder::for(subject: UserRegion::class)
            ->where('type', RegionAssignmentTypes::SE)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'SE Assignments fetched successfully.',
                'user_regions' => UserRegionResource::collection(
                    resource: $userRegions,
                ),
            ],
            status: Http::OK(),
        );
    }
}
