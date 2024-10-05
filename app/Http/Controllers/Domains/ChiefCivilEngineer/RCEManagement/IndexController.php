<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\ChiefCivilEngineer\RCEManagement;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $userRegions  = QueryBuilder::for(subject: UserRegion::class)
           ->where('type', 'RCE')
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'RCE Assignments fetched successfully.',
                'user_regions' => UserRegionResource::collection(
                    resource: $userRegions,
                ),
            ],
            status: Http::OK(),
        );
    }
}
