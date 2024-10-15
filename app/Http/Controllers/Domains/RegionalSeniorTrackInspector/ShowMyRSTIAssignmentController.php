<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalSeniorTrackInspector;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMyRSTIAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_rsti_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', RegionAssignmentTypes::RSTI->value)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'RSTI Assignments fetched successfully.',
                'my_rsti_assignments' => UserRegionResource::collection(
                    resource: $my_rsti_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
