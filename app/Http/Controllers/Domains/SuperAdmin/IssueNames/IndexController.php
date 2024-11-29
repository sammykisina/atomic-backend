<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\IssueNames;

use Domains\TrackAttendant\Models\IssueName;
use Domains\TrackAttendant\Resources\IssueNameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $issue_names  = QueryBuilder::for(subject: IssueName::class)
            ->get();

        return response(
            content: [
                'message' => 'Issue Names fetched successfully.',
                'issue_names' => IssueNameResource::collection(
                    resource: $issue_names,
                ),
            ],
            status: Http::OK(),
        );
    }
}
