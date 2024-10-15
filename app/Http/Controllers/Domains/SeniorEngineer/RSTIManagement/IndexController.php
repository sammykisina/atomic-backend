<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer\RSTIManagement;

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
        $rsti_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('type', RegionAssignmentTypes::RSTI->value)
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->where('owner_id', Auth::id())
            ->get();

        return response(
            content: [
                'message' => 'RSTI Assignments fetched successfully.',
                'rsti_assignments' => UserRegionResource::collection(
                    resource: $rsti_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
