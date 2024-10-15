<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector\Issues;

use Domains\Inspector\Enums\IssueStatuses;
use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\PermanentWayInspector\Models\IssueArea;
use Domains\PermanentWayInspector\Requests\IssueAssignmentRequest;
use Domains\PermanentWayInspector\Requests\SpeedRestrictionSuggestionRequest;
use Domains\PermanentWayInspector\Services\IssueService;
use Domains\RegionalCivilEngineer\Enums\SpeedSuggestionStatuses;
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
    public function assignIssue(IssueAssignmentRequest $request, Issue $issue): Response|HttpException
    {

        $assignment = Assignment::query()->where('issue_id', $issue->id)->first();

        if (
            ! $this->issueService->assignIssueToGangMen(
                issue: $issue,
                gang_men: $assignment ? array_unique(array_merge($request->validated('gang_men'), $assignment->gang_men)) : $request->validated('gang_men'),
            )
        ) {
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
    public function removeAssignment(Request $request, Assignment $assignment): Response|HttpException
    {
        if (
            ! $assignment->update([
                'gang_men' => $request['gang_men'],
            ])
        ) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Sorry, something went wrong. Please try again.",
            );
        }

        if (empty($request['gang_men'])) {
            $assignment->delete();
        }

        return response(
            content: [
                'message' => 'Gang man  removed successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CONFIRM RESOLUTION
     * @param Issue $issue
     * @return Response|HttpException
     */
    public function acceptResolution(Request $request, Issue $issue): Response|HttpException
    {
        if (
            ! $issue->update([
                'status' => IssueStatuses::RESOLVED->value,
            ])
        ) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Sorry, something went wrong. Please try again.",
            );
        }

        $issue_area = IssueArea::query()
            ->where('issue_id', $issue->id)
            ->first();

        if ( ! $this->issueService->openSectionOrStation($issue_area)) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "The issue section or station is not opened for license issuing. Please try again.",
            );
        }

        return response(
            content: [
                'message' => 'Resolution confirmed successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CONFIRM RESOLUTION
     * @param Issue $issue
     * @return Response|HttpException
     */
    public function rejectResolution(Request $request, Issue $issue): Response|HttpException
    {
        if (
            ! $issue->update([
                'status' => IssueStatuses::PENDING->value,
            ])
        ) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Sorry, something went wrong. Please try again.",
            );
        }

        return response(
            content: [
                'message' => 'Resolution rejected successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    public function suggestSpeedRestriction(SpeedRestrictionSuggestionRequest $request, IssueArea $issueArea)
    {
        if ( ! $issueArea->update([
            'speed_suggestion' => $request->validated('speed_suggestion'),
            'speed_suggestion_comment' => $request->validated('speed_suggestion_comment'),
            'speed_suggestion_status' => SpeedSuggestionStatuses::PENDING,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Speed suggestion failed. Please retry.',
            );
        }
        return response(
            content: [
                'message' => 'Speed suggestion was added successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
