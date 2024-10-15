<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Resources\InspectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class SEDashboardController
{
    public function __invoke(Request $request): Response
    {
        $inspections = [];

        $your_rsti = UserRegion::query()
            ->where('type', RegionAssignmentTypes::RSTI->value)
            ->where('owner_id', Auth::id())
            ->with(['line', 'region', 'user'])
            ->get();

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));
            $inspections = Inspection::query()
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule.inspector', function ($query): void {
                    $query->where('region_id', Auth::user()->region_id);
                })
                ->with([
                    'inspectionSchedule.owner.userRegion.owner.userRegion',
                    'inspectionSchedule.inspector',
                    'inspectionSchedule.line',
                    'issues.issueName',
                    'issues.issueArea.station',
                    'issues.issueArea.section',
                    'issues.assignment',
                    'issues.inspection.inspectionSchedule.inspector',
                ])
                ->get();
        } else {
            $startDate = Carbon::now()->subDays(30);
            $inspections = Inspection::query()
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('region_id', Auth::user()->region_id);
                })
                ->with([
                    'inspectionSchedule.owner.userRegion.owner.userRegion',
                    'inspectionSchedule.inspector',
                    'inspectionSchedule.line',
                    'issues.issueName',
                    'issues.issueArea.station',
                    'issues.issueArea.section',
                    'issues.inspection.inspectionSchedule.inspector',
                    'issues.assignment',
                ])
                ->get();
        }

        $number_of_senior_track_attendant = User::query()
            ->where('type', UserTypes::SENIOR_TRACK_ATTENDANT)
            ->where('region_id', Auth::user()->region_id)
            ->count();

        $number_of_track_attendant = User::query()
            ->where('type', UserTypes::TRACK_ATTENDANT)
            ->where('region_id', Auth::user()->region_id)
            ->count();

        return response(
            content: [
                'message' => 'RSTI dashboard fetched successfully.',
                'se_dashboard' => [
                    'your_rsti' => UserRegionResource::collection(
                        resource: $your_rsti,
                    ),
                    'inspections' => InspectionResource::collection(
                        resource: $inspections,
                    ),
                    'number_of_senior_track_attendant' => $number_of_senior_track_attendant,
                    'number_of_track_attendant' => $number_of_track_attendant,
                ],

            ],
            status: Http::OK(),
        );
    }
}
