<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector\Inspections;

use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Resources\InspectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $inspections  = QueryBuilder::for(subject: Inspection::class)
            ->allowedIncludes(includes: [
                'inspectionSchedule.line',
                'inspectionSchedule.inspector',
                AllowedInclude::count(name: 'issuesCount'),
            ])
            ->allowedFilters([
                AllowedFilter::scope('created_at'),
            ])
            ->whereHas('inspectionSchedule', function ($query): void {
                $query->where('owner_id', Auth::id());
            })
            ->get();


        return response(
            content: [
                'message' => 'Inspections fetched successfully.',
                'inspections' => InspectionResource::collection(
                    resource: $inspections,
                ),
            ],
            status: Http::OK(),
        );
    }
}
