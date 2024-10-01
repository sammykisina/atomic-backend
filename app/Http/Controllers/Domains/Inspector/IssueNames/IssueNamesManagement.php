<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector\IssueNames;

use Domains\Inspector\Models\IssueName;
use Domains\Inspector\Requests\CreateOrEditIssueNameRequest;
use Domains\Inspector\Resources\IssueNameResource;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class IssueNamesManagement
{
    /**
     * CREATE ISSUE NAME
     * @param CreateOrEditIssueNameRequest $request
     * @return HttpException|Response
     */
    public function create(CreateOrEditIssueNameRequest $request): HttpException | Response
    {
        $issueName = IssueName::query()->create([
            'name' => $request->validated('name'),
        ]);

        if ( ! $issueName) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue name creation failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Issue name created successfully.',
            ],
            status: Http::CREATED(),
        );

    }

    /**
     * GET ALL ISSUE NAMES
     * @return Response
     */
    public function index(): Response
    {
        $issue_names  = QueryBuilder::for(subject: IssueName::class)
            ->get();

        return response(
            content: [
                'message' => 'Issue names fetched successfully.',
                'issue_names' => IssueNameResource::collection(
                    resource: $issue_names,
                ),
            ],
            status: Http::OK(),
        );
    }
}
