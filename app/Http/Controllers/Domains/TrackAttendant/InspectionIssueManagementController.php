<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\TrackAttendant;

use Domains\SeniorTrackInspector\Services\IssueService;
use Domains\Shared\Concerns\Whatsapp;
use Domains\TrackAttendant\Enums\IssueConditions;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Models\Issue;
use Domains\TrackAttendant\Requests\IssueRequest;
use Domains\TrackAttendant\Services\InspectionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InspectionIssueManagementController
{
    use Whatsapp;

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

            Log::channel(channel: 'custom')->info(message: '=== CREATING ISSUE ===');
            Log::channel(channel: 'custom')->info(message: 'Issue', context: [
                'issue_id' => $issue->id,
                'issue_condition' => $issue->condition,
                'issue_kilometer' => $issue->issue_kilometer,
                'issue_name' => $issue->issueName->name,
            ]);

            $issueArea = IssueService::issueLocation(
                issue: $issue,
            );

            Log::channel(channel: 'custom')->info(message: 'Issue Area', context: [
                'station' =>  $issueArea->station_id,
                'section_id' => $issueArea->section_id,
            ]);

            if ( ! $issueArea) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Issue exact location not found.Please resubmit your issue',
                );
            }

            if (IssueConditions::CRITICAL->value === $issue->condition) {
                if ( ! $this->inspectionService->closeSectionOrStation(issueArea: $issueArea->refresh())) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'There was a problem closing this section or station for license issuing. Please try again',
                    );
                }
            }

            $issue = IssueService::getIssueWithId(
                issue_id: $issue->id,
            );

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
                IMAGE: {{ image_url }}
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

            defer(callback: fn() => $this->sendWhatsAppNotification(
                data: $data,
                template: $template,
                imageUrl: $issue->image_url,
            ));


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
