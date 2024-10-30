<?php

declare(strict_types=1);

namespace Domains\SeniorTrackInspector\Services;

use Domains\SeniorTrackInspector\Models\Assignment;
use Domains\SeniorTrackInspector\Models\IssueArea;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Services\LineService;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Domains\TrackAttendant\Models\Issue;

final class IssueService
{
    /**
     * Issue Location
     * @param Issue $issue
     * @return IssueArea|null
     */
    public static function issueLocation(Issue $issue): IssueArea|null
    {
        $line_id = $issue->inspection->inspectionSchedule->line->id;

        $line = LineService::getLineWithId(line_id: $line_id);
        $issue_kilometer = $issue->issue_kilometer;

        $matching_station = $line->stations->first(
            fn($station) => $issue_kilometer >= $station->start_kilometer && $issue_kilometer <= $station->end_kilometer,
        );

        if ($matching_station) {
            return IssueArea::query()->create([
                'issue_id' => $issue->id,
                'line_id' => $line->id,
                'station_id' => $matching_station->id,
            ]);
        }

        $matching_section = $line->sections->first(
            fn($section) => $issue_kilometer >= $section->start_kilometer && $issue_kilometer <= $section->end_kilometer,
        );

        if ($matching_section) {
            return IssueArea::query()->create([
                'issue_id' => $issue->id,
                'line_id' => $line->id,
                'section_id' => $matching_section->id,
            ]);
        }

        return null;
    }


    /**
     * GET ISSUE WITH ID
     * @param int $issue_id
     * @return Issue
     */
    public static function getIssueWithId(int $issue_id): Issue
    {
        return Issue::query()->where('id', $issue_id)->with([
            'issueName',
            'inspection.inspectionSchedule.inspector',
            'inspection.inspectionSchedule.owner',
            'inspection.inspectionSchedule.line',
            'assignment.resolver',
            'issueArea.line',
            'issueArea.station',
            'issueArea.section',
        ])->first();
    }
    /**
     * ASSIGN GANG MEN TO ISSUE
     * @param Issue $issue
     * @param array $gang_men
     * @return void
     */
    public function assignIssueToGangMen(Issue $issue, array $gang_men): Assignment
    {
        return Assignment::query()->updateOrCreate(
            ['issue_id' => $issue->id],
            [
                'gang_men' => $gang_men,
            ],
        );
    }

    /**
     * OPEN SECTION OR STATION FROM INTERDICTION
     * @param IssueArea $issueArea
     * @return bool
     */
    public function openSectionOrStation(IssueArea $issueArea): bool
    {
        $section = null;
        $station = null;

        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            return $station->update(attributes: [
                'status' => StationSectionLoopStatuses::GOOD->value,
            ]);
        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            return $section->update(attributes: [
                'status' => StationSectionLoopStatuses::GOOD->value,
            ]);

        }
        return false;
    }
}
