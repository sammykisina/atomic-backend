<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Domains\Inspector\Enums\IssueConditions;
use Domains\Inspector\Enums\IssueStatuses;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Models\Issue;
use Domains\Inspector\Resources\IssueResource;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class RPWIDashboardController
{
    public function __invoke(Request $request): Response
    {
        $completed_inspections = null;
        $incomplete_inspections = null;

        $total_reported_issues = null;
        $total_unassigned_issues = null;
        $total_pending_issues = null;
        $total_resolved_issues = null;

        $your_pwis = UserRegion::query()
            ->where('type', 'PWI')
            ->where('owner_id', Auth::id())
            ->when(request('start_kilometer'), function ($query, $startKilometer): void {
                $query->where('start_kilometer', '=', $startKilometer);
            })
            ->when(request('end_kilometer'), function ($query, $endKilometer): void {
                $query->where('end_kilometer', '=', $endKilometer);
            })
            ->with(['line', 'region', 'user'])
            ->get();

        $pwi_ids = $your_pwis->pluck('user_id');

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));

            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereNull('end_time')
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereDate('created_at', $date)
                ->has(relation: 'assignment')
                ->count();


            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereDate('created_at', $date)
                ->count();


        } else {
            $startDate = Carbon::now()->subDays(30);
            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->count();

            $incomplete_inspections = Inspection::query()
                ->whereNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->count();

            $total_reported_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_unassigned_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->doesntHave('assignment')
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_resolved_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_pending_issues = Issue::query()
                ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids): void {
                    $query->whereIn('owner_id', $pwi_ids);
                })
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->has(relation: 'assignment')
                ->count();
        }

        $start_kilometer = request('start_kilometer');
        $end_kilometer = request('end_kilometer');

        $critical_issues = Issue::query()
            ->whereHas('inspection.inspectionSchedule', function ($query) use ($pwi_ids, $start_kilometer, $end_kilometer): void {
                $query->whereIn('owner_id', $pwi_ids)
                    ->whereHas('owner.userRegion', function ($query) use ($start_kilometer, $end_kilometer): void {
                        if ($start_kilometer) {
                            $query->where('start_kilometer', '=', $start_kilometer);
                        }
                        if ($end_kilometer) {
                            $query->where('end_kilometer', '=', $end_kilometer);
                        }
                    });
            })
            ->where('condition', IssueConditions::CRITICAL->value)
            ->with([
                'issueName',
                'inspection.inspectionSchedule.inspector',
                'inspection.inspectionSchedule.owner.userRegion.owner',
                'inspection.inspectionSchedule.line',
            ])
            ->get();

        $number_of_gang_persons = User::query()
            ->where('type', UserTypes::GANG_MAN)
            ->where('region_id', Auth::user()->region_id)
            ->count();

        $number_of_inspectors = User::query()
            ->where('type', UserTypes::INSPECTOR)
            ->where('region_id', Auth::user()->region_id)
            ->count();


        return response(
            content: [
                'message' => 'RPWI dashboard fetched successfully.',
                'rpwi_dashboard' => [
                    'your_pwis' => UserRegionResource::collection(
                        resource: $your_pwis,
                    ),
                    'completed_inspections' => $completed_inspections,
                    'total_reported_issues' => $total_reported_issues,
                    'total_unassigned_issues' => $total_unassigned_issues,
                    'total_resolved_issues' => $total_resolved_issues,
                    'total_pending_issues' => $total_pending_issues,
                    'critical_issues' => IssueResource::collection(
                        resource: $critical_issues,
                    ),
                    'incomplete_inspections' => $incomplete_inspections,
                    'number_of_gang_persons' => $number_of_gang_persons,
                    'number_of_inspectors' => $number_of_inspectors,
                ],

            ],
            status: Http::OK(),
        );
    }
}
