<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\Inspector\Enums\IssueStatuses;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Models\Issue;
use Domains\Inspector\Resources\InspectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class PWADashboardController
{
    public function __invoke(Request $request): Response
    {
        $completed_inspections = Inspection::query()
            ->whereNotNull('end_time')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $total_reported_issues = Issue::query()
            ->whereDate('created_at', Carbon::today())
            ->count();

        $total_resolved_issues = Issue::query()
            ->where('status', IssueStatuses::RESOLVED->value)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $total_pending_issues = Issue::query()
            ->where('status', IssueStatuses::PENDING->value)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $recent_inspections = Inspection::query()
            ->with('inspectionSchedule.inspector')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

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
                ],
            ],
            status: Http::OK(),
        );

    }
}
