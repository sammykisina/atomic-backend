<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\IssueNames;

use Domains\TrackAttendant\Models\IssueName;
use Domains\TrackAttendant\Resources\IssueNameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * CREATE ISSUE NAME
     * @param Request $request
     * @return HttpException|Response
     */
    public function create(Request $request): HttpException | Response
    {

        $issue_name = IssueName::create([
            'name' => $request->get('name'),
        ]);

        if ( ! $issue_name) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue name creation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Issue name created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT ISSUE NAME
     * @param Request $request
     * @param IssueName $issueName
     * @return HttpException|Response
     */
    public function edit(Request $request, IssueName $issueName): HttpException | Response
    {
        if ( ! $issueName->update([
            'name' => $request->get('name'),
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue name update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Issue name updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW ISSUE NAME
     * @param IssueName $issueName
     * @return Response
     */
    public function show(IssueName $issueName): Response | HttpException
    {
        return response(
            content: [
                'message' => 'Issue name fetched successfully.',
                'issue_name' => new IssueNameResource(
                    resource: $issueName,
                ),
            ],
            status: Http::OK(),
        );
    }
}
