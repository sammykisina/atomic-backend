<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorTrackInspector;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMySTIAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_sti_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', RegionAssignmentTypes::STI->value)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'STI Assignments fetched successfully.',
                'my_sti_assignments' => UserRegionResource::collection(
                    resource: $my_sti_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
