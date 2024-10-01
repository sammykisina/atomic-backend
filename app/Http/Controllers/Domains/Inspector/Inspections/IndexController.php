<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector\Inspections;

use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Resources\InspectionResource;
use Domains\Inspector\Services\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $inspection_schedule = InspectionService::getInspectionSchedule();

        if ( ! $inspection_schedule) {
            return response(
                content: [
                    'message' => 'Inspections fetched successfully.',
                    'inspections' => [],
                ],
                status: Http::OK(),
            );
        }

        $inspections  = QueryBuilder::for(subject: Inspection::class)
            ->where('inspection_schedule_id', $inspection_schedule->id)
            ->allowedIncludes(includes: [
                'issues.issueName',
            ])->get();

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
