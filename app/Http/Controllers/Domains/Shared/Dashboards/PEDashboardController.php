<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\PrincipleEngineer\Resources\UserRegionResource;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Domains\TrackAttendant\Enums\IssueConditions;
use Domains\TrackAttendant\Enums\IssueStatuses;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Models\Issue;
use Domains\TrackAttendant\Resources\IssueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class PEDashboardController
{
    public function __invoke(Request $request): Response
    {
        $completed_inspections = null;
        $incomplete_inspections = null;

        $total_reported_issues = null;
        $total_unassigned_issues = null;
        $total_pending_issues = null;

        $total_resolved_issues = null;

        $your_se = UserRegion::query()
            ->where('type', RegionAssignmentTypes::SE)
            ->with(['line','region','user'])
            ->get();

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));

            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereNull('end_time')
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereDate('created_at', $date)
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereDate('created_at', $date)
                ->has(relation: 'assignment')
                ->count();

        } else {
            $startDate = Carbon::now()->subDays(30);
            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->doesntHave('assignment')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->has(relation: 'assignment')
                ->count();

            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule.inspector', function ($query) use ($request): void {
                    $query->where('region_id', $request->query('region_id'));
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();
        }

        $critical_issues = Issue::query()
            ->whereHas('inspection.inspectionSchedule', function ($query) use ($request): void {
                $query->where('region_id', $request->query('region_id'));
            })
            ->where('condition', IssueConditions::CRITICAL->value)
            ->with([
                'issueName',
                'inspection.inspectionSchedule.inspector',
                'inspection.inspectionSchedule.owner.userRegion.owner',
                'inspection.inspectionSchedule.line',
                'inspection.inspectionSchedule.owner.userRegion.owner.userRegion.owner',
            ])
            ->get();


        $number_of_senior_track_attendant = User::query()
            ->where('type', UserTypes::SENIOR_TRACK_ATTENDANT)
            ->count();

        $number_of_track_attendant = User::query()
            ->where('type', UserTypes::TRACK_ATTENDANT)
            ->count();

        return response(
            content: [
                'message' => 'PE dashboard fetched successfully.',
                'pe_dashboard' => [
                    'your_se' => UserRegionResource::collection(
                        resource: $your_se,
                    ),
                    'completed_inspections' => $completed_inspections,
                    'incomplete_inspections' => $incomplete_inspections,

                    'total_reported_issues' => $total_reported_issues,
                    'total_unassigned_issues' => $total_unassigned_issues,
                    'total_resolved_issues' => $total_resolved_issues,

                    'total_pending_issues' => $total_pending_issues,
                    'critical_issues' => IssueResource::collection(
                        resource: $critical_issues,
                    ),
                    'number_of_senior_track_attendant' => $number_of_senior_track_attendant,
                    'number_of_track_attendant' => $number_of_track_attendant,
                ],

            ],
            status: Http::OK(),
        );
    }
}
