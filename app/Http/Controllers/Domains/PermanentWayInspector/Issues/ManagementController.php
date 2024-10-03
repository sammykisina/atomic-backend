<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector\Issues;

use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\PermanentWayInspector\Requests\IssueAssignmentRequest;
use Domains\PermanentWayInspector\Services\IssueService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected IssueService $issueService,
    ) {}

    /**
     * ISSUE ASSIGNMENT
     * @param IssueAssignmentRequest $request
     * @param Issue $issue
     * @return void
     */
    public function assignIssue(IssueAssignmentRequest $request, Issue $issue): Response | HttpException
    {

        $assignment = Assignment::query()->where('issue_id', $issue->id)->first();

        if ( ! $this->issueService->assignIssueToGangMen(
            issue: $issue,
            gang_men: $assignment ?  array_unique(array_merge($request->validated('gang_men'), $assignment->gang_men)) : $request->validated('gang_men'),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Sorry, something went wrong. Please try again.",
            );
        }

        return response(
            content: [
                'message' => 'Issue assigned to gang man(s) successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * REMOVE GANG MAN
     * @param Assignment $assignment
     * @param IssueAssignmentRequest $request
     * @return Response|HttpException
     */
    public function removeAssignment(Request $request, Assignment $assignment): Response | HttpException
    {
        if ( ! $assignment->update([
            'gang_men' => $request['gang_men'],
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Sorry, something went wrong. Please try again.",
            );
        }

        return response(
            content: [
                'message' => 'Gang man  removed successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
