<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\Inspector\Enums\IssueStatuses;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Models\Issue;
use Domains\Inspector\Resources\InspectionResource;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Domains\Shared\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class PWADashboardController
{
    public function __invoke(Request $request): Response
    {
        $completed_inspections = null;
        $total_reported_issues = null;
        $total_resolved_issues = null;
        $total_pending_issues = null;

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));

            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereDate('created_at', $date)
                ->count();

            $total_reported_issues = Issue::query()
                ->whereDate('created_at', $date)
                ->where('status', IssueStatuses::PENDING->value)
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_resolved_issues = Issue::query()
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereDate('created_at', $date)
                ->count();

            $total_pending_issues = Issue::query()
                ->where('status', IssueStatuses::PENDING->value)
                ->has(relation: 'assignment')
                ->whereDate('created_at', $date)
                ->count();
        } else {
            // Get data for the last 30 days
            $startDate = Carbon::now()->subDays(30);

            $completed_inspections = Inspection::query()
                ->whereNotNull('end_time')
                ->whereBetween('created_at', [$startDate, Carbon::now()])

                ->count();

            $total_reported_issues = Issue::query()
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->doesntHave(relation: 'assignment')
                ->count();

            $total_resolved_issues = Issue::query()
                ->where('status', IssueStatuses::RESOLVED->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->count();

            $total_pending_issues = Issue::query()
                ->where('status', IssueStatuses::PENDING->value)
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->has(relation: 'assignment')
                ->count();
        }

        $recent_inspections = Inspection::query()
            ->with('inspectionSchedule.inspector')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $your_gang_man = User::query()->where('type', UserTypes::GANG_MAN)->get();
        $your_inspectors = User::query()->where('type', UserTypes::INSPECTOR)->get();

        return response(
            content: [
                'message' => 'PWA dashboard fetched successfully.',
                'pwa_dashboard' => [
                    'completed_inspections' => $completed_inspections,
                    'total_reported_issues' => $total_reported_issues,
                    'total_resolved_issues' => $total_resolved_issues,
                    'total_pending_issues' => $total_pending_issues,
                    'recent_inspections' =>  InspectionResource::collection(
                        resource: $recent_inspections,
                    ),
                    'your_gang_man' => UserResource::collection(
                        resource: $your_gang_man,
                    ),
                    'your_inspectors' => UserResource::collection(
                        resource: $your_inspectors,
                    ),
                ],
            ],
            status: Http::OK(),
        );
    }
}
