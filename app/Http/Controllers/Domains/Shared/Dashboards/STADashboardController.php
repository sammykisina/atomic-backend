<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Domains\SeniorTrackInspector\Models\Assignment;
use Domains\SeniorTrackInspector\Resources\AssignmentResource;
use Domains\TrackAttendant\Enums\IssueStatuses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class STADashboardController
{
    public function __invoke(): Response
    {
        $assignments = Assignment::query()
            ->whereJsonContains('gang_men', Auth::id())
            ->whereHas('issue', function ($query): void {
                $query->where('status', IssueStatuses::PENDING->value);
            })
            ->with([
                'issue.issueName',
                'issue.issueArea.line',
                'issue.issueArea.station',
                'issue.issueArea.section',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response(
            content: [
                'message' => 'STA dashboard fetched successfully.',
                'sta_dashboard' => [
                    'assignments' => AssignmentResource::collection(
                        resource: $assignments,
                    ),
                ],
            ],
            status: Http::OK(),
        );
    }
}
