<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Domains\Inspector\Enums\IssueStatuses;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\PermanentWayInspector\Resources\AssignmentResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class GangManDashboardController
{
    public function __invoke(): Response
    {
        $assignments = Assignment::query()
            ->whereJsonContains('gang_men', Auth::id())
            ->whereHas('issue', function ($query): void {
                $query->where('status', IssueStatuses::PENDING->value);
            })
            ->with('issue.issueName')
            ->orderBy('created_at', 'desc')
            ->get();

        return response(
            content: [
                'message' => 'Gand man dashboard fetched successfully.',
                'gang_man_dashboard' => [
                    'assignments' => AssignmentResource::collection(
                        resource: $assignments,
                    ),
                ],
            ],
            status: Http::OK(),
        );
    }
}
