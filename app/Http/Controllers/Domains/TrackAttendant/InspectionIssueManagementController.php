<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\TrackAttendant;

use Domains\SeniorTrackInspector\Services\IssueService;
use Domains\TrackAttendant\Enums\IssueConditions;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Models\Issue;
use Domains\TrackAttendant\Requests\IssueRequest;
use Domains\TrackAttendant\Services\InspectionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http as FacadesHttp;
use Illuminate\Support\Facades\Log;
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


            $issue = IssueService::getIssueWithId(
                issue_id: $issue->id,
            );

            defer(fn() => $this->sendWhatsAppNotification(issue: $issue));


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
                DESCRIPTION: {{ description }}
                KILOMETER: {{ kilometer }}
                LOCATION: {{ location }}
                STI: {{ sti }}
                TA: {{ ta }}
                REPORTED AT: {{ reported_at }}
                EOT;

        $area_station = $issue->issueArea->station;
        $area_section = $issue->issueArea->section;

        $data = [
            'issue_id' => $issue->id,
            'type' => 'ISSUE REPORTED',
            'condition' => $issue->condition,
            'name' => $issue->issueName->name,
            'description' => $issue->description,
            'kilometer' => "KM " . $issue->issue_kilometer,
            'location' => $issue->issueArea->line->name . " (" . ($area_station ? $area_station->name : $area_section->fullname) . " )",
            'ta' => $issue->inspection->inspectionSchedule->inspector->fullname,
            'sti' => $issue->inspection->inspectionSchedule->owner->fullname,
            'reported_at' => $issue->created_at->isoFormat('MMM Do YYYY HH:mm'),
        ];

        Log::info('issue_data', $data);


        $message = str_replace(
            search: ['{{ issue_id }}', '{{ type }}', '{{ condition }}', '{{ name }}', '{{ description }}', '{{ kilometer }}', '{{ location }}', '{{ sti }}', '{{ ta }}', '{{ reported_at }}'],
            replace: [$data['issue_id'], $data['type'], $data['condition'], $data['name'], $data['description'], $data['kilometer'], $data['location'], $data['sti'], $data['ta'], $data['reported_at']],
            subject: $template,
        );


        $response = FacadesHttp::withHeaders([
            'Authorization' => 'Bearer ' . config('whatsapp.wassenger.auth_token'),
            'Accept' => 'application/json',
        ])->post(config('whatsapp.wassenger.base_url') . '/messages', [
            'group' => config('whatsapp.wassenger.group_url'),
            'message' => $message,
            'media' => [
                'url' => $issue->image_url,
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
