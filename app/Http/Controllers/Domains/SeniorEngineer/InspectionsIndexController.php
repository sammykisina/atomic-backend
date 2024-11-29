<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer;

use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Resources\InspectionResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class InspectionsIndexController
{
    public function __invoke(): Response
    {
        $inspections = Inspection::query()
            ->whereHas('inspectionSchedule.inspector', function ($query): void {
                $query->where('region_id', Auth::user()->region_id);
            })
            ->with([
                'inspectionSchedule.owner.userRegion.owner.userRegion',
                'inspectionSchedule.inspector',
                'inspectionSchedule.line',
                'issues.issueName',
                'issues.assignment',
            ])
            ->get();

        return response(
            content: [
                'message' => 'RSTI Inspections fetched successfully.',
                'rsti_inspections' => InspectionResource::collection(
                    resource: $inspections,
                ),

            ],
            status: Http::OK(),
        );
    }
}
