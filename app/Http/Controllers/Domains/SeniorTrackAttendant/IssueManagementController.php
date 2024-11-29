<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorTrackAttendant;

use Domains\SeniorTrackInspector\Models\Assignment;
use Domains\SeniorTrackInspector\Models\IssueArea;
use Domains\SeniorTrackInspector\Resources\AssignmentResource;
use Domains\Shared\Concerns\Whatsapp;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Domains\TrackAttendant\Enums\IssueStatuses;
use Domains\TrackAttendant\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class IssueManagementController
{
    use Whatsapp;

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
                IMAGE: {{ image_url }}
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

            defer(fn() => $this->sendWhatsAppNotification(
                data: $data,
                template: $template,
                imageUrl: $issue->assignment->image_url,
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
                'issue.issueArea.station',
                'issue.issueArea.section',
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
     * MARK AREA UNDER MAINTENANCE
     * @param IssueArea $issueArea
     * @return Response
     */
    public function markAreaUnderMaintenance(IssueArea $issueArea): Response
    {
        $section = null;
        $station = null;


        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            $station->update(attributes: [
                'status' => StationSectionLoopStatuses::UNDER_MAINTENANCE->value,
            ]);


            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MARK_AREA_UNDER_MAINTENANCE,
                'resourceble_id' => $station->id,
                'resourceble_type' => get_class(object: $station),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $station->name,
            ]);

            return response(
                content: [
                    'message' => 'Issue area marked under maintenance successfully.',
                ],
                status: Http::ACCEPTED(),
            );

        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            $section->update(attributes: [
                'status' => StationSectionLoopStatuses::UNDER_MAINTENANCE->value,
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MARK_AREA_UNDER_MAINTENANCE,
                'resourceble_id' => $section->id,
                'resourceble_type' => get_class(object: $section),
                'actor_id' => Auth::id(),
                'receiver_id' => Auth::id(),
                'current_location' => $section->start_name . " - " . $section->end_name,
            ]);

            return response(
                content: [
                    'message' => 'Issue area marked under maintenance successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        return response(
            content: [
                'message' => 'Something went wrong. Issue area not marked under maintenance',
            ],
            status: Http::NOT_ACCEPTABLE(),
        );
    }
}
