<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMySEAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_se_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', RegionAssignmentTypes::SE)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'SE Assignments fetched successfully.',
                'my_se_assignments' => UserRegionResource::collection(
                    resource: $my_se_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
