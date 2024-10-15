<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Domains\Shared\Resources\UserResource;
use Domains\TrackAttendant\Enums\IssueStatuses;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Models\Issue;
use Domains\TrackAttendant\Resources\InspectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class STIDashboardController
{
    public function __invoke(Request $request): Response
    {
        $completed_inspections = null;
        $incomplete_inspections = null;

        $total_reported_issues = null;
        $total_unassigned_issues = null;
        $total_pending_issues = null;
        $total_resolved_issues = null;

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));

            $completed_inspections = Inspection::query()
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->whereNotNull('end_time')
                ->whereDate('created_at', $date)
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->whereNull('end_time')
                ->whereDate('created_at', $date)
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->has(relation: 'assignment')
                ->whereDate('created_at', $date)
                ->count();

            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereDate('created_at', $date)
                ->count();


        } else {
            // Get data for the last 30 days
            $startDate = Carbon::now()->subDays(30);

            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->has(relation: 'assignment')
                ->count();

            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query): void {
                    $query->where('owner_id', Auth::id());
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();
        }


        $recent_inspections = Inspection::query()
            ->with('inspectionSchedule.inspector')
            ->whereHas('inspectionSchedule', function ($query): void {
                $query->where('owner_id', Auth::id());
            })
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $number_of_senior_track_attendant = User::query()
            ->where('type', UserTypes::SENIOR_TRACK_ATTENDANT)
            ->where('region_id', Auth::user()->region_id)
            ->get();

        $number_of_track_attendant = User::query()
            ->where('type', UserTypes::TRACK_ATTENDANT)
            ->where('region_id', Auth::user()->region_id)
            ->get();

        return response(
            content: [
                'message' => 'PWA dashboard fetched successfully.',
                'pwa_dashboard' => [
                    'completed_inspections' => $completed_inspections,
                    'incomplete_inspections' => $incomplete_inspections,
                    'total_reported_issues' => $total_reported_issues,
                    'total_unassigned_issues' => $total_unassigned_issues,
                    'total_pending_issues' => $total_pending_issues,
                    'total_resolved_issues' => $total_resolved_issues,
                    'recent_inspections' =>  InspectionResource::collection(
                        resource: $recent_inspections,
                    ),
                    'number_of_senior_track_attendant' => UserResource::collection(
                        resource: $number_of_senior_track_attendant,
                    ),
                    'number_of_track_attendant' => UserResource::collection(
                        resource: $number_of_track_attendant,
                    ),
                ],
            ],
            status: Http::OK(),
        );
    }
}
