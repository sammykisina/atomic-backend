<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PrincipleEngineer;

use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Resources\InspectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class InspectionsIndexController
{
    public function __invoke(Request $request): Response
    {
        $inspections = Inspection::query()
            ->whereHas('inspectionSchedule', function ($query) use ($request): void {
                $query->where('region_id', $request->query('region_id'));
            })
            ->with([
                'inspectionSchedule.owner.userRegion.owner',
                'inspectionSchedule.inspector',
                'inspectionSchedule.line',
                'issues.issueName',
                'issues.assignment',
                'inspectionSchedule.owner.userRegion.owner.userRegion.owner.region',
            ])
            ->get();

        return response(
            content: [
                'message' => 'SE Inspections fetched successfully.',
                'se_inspections' => InspectionResource::collection(
                    resource: $inspections,
                ),

            ],
            status: Http::OK(),
        );
    }
}
