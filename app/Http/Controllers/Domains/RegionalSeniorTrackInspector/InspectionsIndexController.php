<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalSeniorTrackInspector;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Resources\InspectionResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class InspectionsIndexController
{
    public function __invoke(): Response
    {
        $sti_ids =  UserRegion::query()
            ->where('type', RegionAssignmentTypes::STI->value)
            ->where('owner_id', Auth::id())
            ->pluck('user_id');

        $inspections = Inspection::query()
            ->with(['inspectionSchedule.inspector','inspectionSchedule.line', 'inspectionSchedule.owner.userRegion', 'issues.issueName', 'issues.assignment'])
            ->whereHas('inspectionSchedule', function ($query) use ($sti_ids): void {
                $query->whereIn('owner_id', $sti_ids);
            })
            ->get();

        return response(
            content: [
                'message' => 'STI Inspections fetched successfully.',
                'sti_inspections' => InspectionResource::collection(
                    resource: $inspections,
                ),

            ],
            status: Http::OK(),
        );
    }
}
