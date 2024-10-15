<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector;

use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\SeniorTrackInspector\Resources\InspectionScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

final class InspectionScheduleController
{
    public function __invoke(Request $request): Response
    {
        $inspectionSchedules  = QueryBuilder::for(subject: InspectionSchedule::class)
            ->where('inspector_id', Auth::id())
            ->allowedIncludes(includes: [
                'line',
                'inspector',
                AllowedInclude::count(name: 'inspectionsCount'),
            ])
            ->allowedFilters([
                AllowedFilter::exact('line_id'),

            ])
            ->first();

        return response(
            content: [
                'message' => 'Inspection schedule fetched successfully.',
                'inspection_schedule' => new InspectionScheduleResource(
                    resource: $inspectionSchedules,
                ),
            ],
            status: Http::OK(),
        );
    }
}
