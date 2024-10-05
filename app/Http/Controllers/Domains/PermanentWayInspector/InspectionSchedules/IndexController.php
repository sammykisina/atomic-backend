<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector\InspectionSchedules;

use Domains\PermanentWayInspector\Models\InspectionSchedule;
use Domains\PermanentWayInspector\Resources\InspectionScheduleResource;
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
        $inspectionSchedules  = QueryBuilder::for(subject: InspectionSchedule::class)
            ->allowedIncludes(includes: [
                'line',
                'inspector',
                AllowedInclude::count(name: 'inspectionsCount'),
            ])
            ->where('owner_id', Auth::id())
            ->allowedSorts('start_kilometer')
            ->allowedFilters([
                AllowedFilter::exact('line_id'),

            ])
            ->get();

        return response(
            content: [
                'message' => 'Inspection schedules fetched successfully.',
                'inspection_schedules' => InspectionScheduleResource::collection(
                    resource: $inspectionSchedules,
                ),
            ],
            status: Http::OK(),
        );
    }
}
