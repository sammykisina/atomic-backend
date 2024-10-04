<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector\Issues;

use Domains\Inspector\Models\Issue;
use Domains\Inspector\Resources\IssueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                'assignment',
            ])
            ->orderBy('created_at', 'desc')
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
