<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorTrackInspector\Issues;

use Domains\TrackAttendant\Models\Issue;
use Domains\TrackAttendant\Resources\IssueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $issues  = QueryBuilder::for(subject: Issue::class)
            ->allowedIncludes(includes: [
                'issueName',
                'inspection.inspectionSchedule.inspector',
                'inspection.inspectionSchedule.line',
                'assignment.resolver',
                'issueArea.line',
                'issueArea.station',
                'issueArea.section',
            ])
            ->whereHas('inspection.inspectionSchedule', function ($query): void {
                $query->where('owner_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->orderByRaw("FIELD(status, 'DRAFT', 'PENDING', 'RESOLVED')")
            ->allowedFilters([
            ])
            ->get();

        return response(
            content: [
                'message' => 'Issues fetched successfully.',
                'issues' => IssueResource::collection(
                    resource: $issues,
                ),
            ],
            status: Http::OK(),
        );
    }
}
