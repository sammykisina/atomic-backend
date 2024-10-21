<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorTrackAttendant;

use Domains\SeniorTrackInspector\Models\Assignment;
use Domains\SeniorTrackInspector\Resources\AssignmentResource;
use Domains\SeniorTrackInspector\Services\IssueService;
use Domains\TrackAttendant\Enums\IssueStatuses;
use Domains\TrackAttendant\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http as FacadesHttp;
use Illuminate\Support\Facades\Log;
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

            defer(fn() => $this->sendWhatsAppNotification(
                issue: IssueService::getIssueWithId(
                    issue_id: $issue->id,
                ),
            ));

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


    /**
     * SEND WHATS APP NOTIFICATION OF AN ISSUE
     * @param Issue $issue
     * @return void
     */
    public function sendWhatsAppNotification(Issue $issue): void
    {
        $template = <<<EOT
                ISS#: {{ issue_id }}
                TYPE: {{ type }}
                CONDITION: {{ condition }}
                NAME: {{ name }}
                SOLUTION COMMENT: {{ solution_comment }}
                KILOMETER: {{ kilometer }}
                LOCATION: {{ location }}
                STI: {{ sti }}
                STA: {{ sta }}
                EOT;

        $area_station = $issue->issueArea->station;
        $area_section = $issue->issueArea->section;

        $data = [
            'issue_id' => $issue->id,
            'type' => 'ISSUE SOLUTION',
            'condition' => $issue->condition,
            'name' => $issue->issueName->name,
            'solution_comment' => $issue->assignment->comment,
            'kilometer' => "KM " . $issue->issue_kilometer,
            'location' => $issue->issueArea->line->name . " (" . ($area_station ? $area_station->name : $area_section->fullname) . " )",
            'sti' => $issue->inspection->inspectionSchedule->owner->fullname,
            'sta' => $issue->assignment->resolver->fullname,
        ];

        $message = str_replace(
            search: ['{{ issue_id }}', '{{ type }}', '{{ condition }}', '{{ name }}', '{{ solution_comment }}', '{{ kilometer }}', '{{ location }}', '{{ sti }}', '{{ sta }}'],
            replace: [$data['issue_id'], $data['type'], $data['condition'], $data['name'], $data['solution_comment'], $data['kilometer'], $data['location'], $data['sti'], $data['sta']],
            subject: $template,
        );

        $response = FacadesHttp::withHeaders([
            'Authorization' => 'Bearer ' . config('whatsapp.wassenger.auth_token'),
            'Accept' => 'application/json',
        ])->post(config('whatsapp.wassenger.base_url') . '/messages', [
            'group' => config('whatsapp.wassenger.group_url'),
            'message' => $message,
            'media' => [
                'url' => $issue->assignment->image_url,
                "expiration" => "7d",
                "viewOnce" => false,
            ],
        ]);

        if ($response->successful()) {
            Log::info('Message send');
        } else {
            Log::info('Failed to send message: ' . $response->body());
        }

    }
}
