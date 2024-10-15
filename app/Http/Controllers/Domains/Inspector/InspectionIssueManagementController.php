<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector;

use Domains\Inspector\Enums\IssueConditions;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Models\Issue;
use Domains\Inspector\Requests\IssueRequest;
use Domains\Inspector\Services\InspectionService;
use Domains\PermanentWayInspector\Services\IssueService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InspectionIssueManagementController
{
    public function __construct(
        protected InspectionService $inspectionService,
    ) {}

    /**
     * REPORT ISSUE
     * @param IssueRequest $request
     * @param Inspection $inspection
     * @return HttpException|Response
     */
    public function create(IssueRequest $request, Inspection $inspection): HttpException | Response
    {
        $issue = DB::transaction(function () use ($request, $inspection): ?Issue {
            $issue = $this->inspectionService->createInspectionIssue(
                inspection: $inspection,
                inspectionIssueData: [
                    'condition' => $request->validated('condition'),
                    'description' => $request->validated('description') ?? null,
                    'latitude' => $request->validated('latitude'),
                    'longitude' => $request->validated('longitude'),
                    'image_url' => $request->validated('image_url') ?? null,
                    'issue_name_id' => $request->validated('issue_name_id'),
                    'issue_kilometer' => $request->validated('issue_kilometer'),
                ],
            );

            $issueArea = IssueService::issueLocation(
                issue: $issue,
            );

            if ( ! $issueArea) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Issue exact location not found.Please resubmit your issue',
                );
            }

            if (IssueConditions::CRITICAL->value === $issue->condition) {
                if ( ! $this->inspectionService->closeSectionOrStation(issueArea: $issueArea)) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'There was a problem closing this section or station for license issuing. Please try again',
                    );
                }
            }

            return $issue;
        });

        if ( ! $issue) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue not reported successfully. Please retry adding the issue. Incase the problem persists, you can navigate to your list inspections and add this issue manually.',
            );
        }

        return Response(
            content: [
                'message' => 'Issue reported successfully. You can continue with your inspection.',
            ],
            status: Http::CREATED(),
        );
    }
}
