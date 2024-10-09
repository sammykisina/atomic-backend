<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalPermanentWayInspector;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Resources\InspectionResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class InspectionsIndexController
{
    public function __invoke(): Response
    {
        $pwi_ids =  UserRegion::query()
            ->where('type', 'PWI')
            ->where('owner_id', Auth::id())
            ->pluck('user_id');

        $inspections = Inspection::query()
            ->with(['inspectionSchedule.inspector','inspectionSchedule.line', 'inspectionSchedule.owner.userRegion', 'issues.issueName', 'issues.assignment'])
            ->whereHas('inspectionSchedule', function ($query) use ($pwi_ids): void {
                $query->whereIn('owner_id', $pwi_ids);
            })
            ->get();

        return response(
            content: [
                'message' => 'RPWI Inspections fetched successfully.',
                'rpwi_inspections' => InspectionResource::collection(
                    resource: $inspections,
                ),

            ],
            status: Http::OK(),
        );
    }
}
