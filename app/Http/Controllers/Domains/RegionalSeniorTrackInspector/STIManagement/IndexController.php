<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalSeniorTrackInspector\STIManagement;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $sti_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('type', RegionAssignmentTypes::STI->value)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->where('owner_id', Auth::id())
            ->get();

        return response(
            content: [
                'message' => 'STI Assignments fetched successfully.',
                'sti_assignments' => UserRegionResource::collection(
                    resource: $sti_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
