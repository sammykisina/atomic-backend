<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\GangMan;

use Domains\Inspector\Enums\IssueStatuses;
use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\PermanentWayInspector\Resources\AssignmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class IssueManagementController
{
    /**
     * RESOLVE ISSUE
     * @param Request $request
     * @param Issue $issue
     * @param Assignment $assignment
     * @return Response|HttpException
     */
    public function resolve(Request $request, Issue $issue, Assignment $assignment): Response | HttpException
    {
        $resolved = DB::transaction(function () use ($request, $issue, $assignment): bool {
            $issue->update([
                'status' => IssueStatuses::DRAFT->value,
            ]);

            $assignment->update([
                'image_url' => $request->get(key: 'image_url'),
                'resolver_id' => Auth::id(),
                'comment' => $request->get(key: 'comment'),
            ]);

            return true;
        });

        if ( ! $resolved) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue not resolved. Please try again.',
            );
        }

        return Response(
            content: [
                'message' => 'Issue Resolved successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * GET ISSUES HISTORY
     * @return Response
     */
    public function index(): Response
    {
        $issues_history = Assignment::query()
            ->whereJsonContains('gang_men', Auth::id())
            ->whereHas('issue', function ($query): void {
                $query->whereIn('status', [
                    IssueStatuses::RESOLVED->value,
                    IssueStatuses::DRAFT->value,
                ]);
            })
            ->with(relations: [
                'issue.issueName',
                'issue.inspection.inspectionSchedule.line',
                'issue.inspection.inspectionSchedule.inspector',
                'resolver',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response(
            content: [
                'message' => 'Issues history fetched successfully.',
                'issues_history' => AssignmentResource::collection(
                    resource: $issues_history,
                ),
            ],
            status: Http::OK(),
        );
    }
}
