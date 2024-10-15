<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Services;

use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\PermanentWayInspector\Models\IssueArea;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;

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

        $line = Line::query()
            ->where('id', $line_id)
            ->with(['stations', 'sections'])
            ->first();


        foreach ($line->stations as $station) {
            if ($issue->issue_kilometer >= $station->start_kilometer && $issue->issue_kilometer <= $station->end_kilometer) {
                return IssueArea::query()
                    ->create([
                        'issue_id' => $issue->id,
                        'line_id' => $line->id,
                        'station_id' => $station->id,
                    ]);
            }
        }

        foreach ($line->sections as $section) {
            if ($issue->issue_kilometer >= $section->start_kilometer && $issue->issue_kilometer <= $section->end_kilometer) {
                return IssueArea::query()
                    ->create([
                        'issue_id' => $issue->id,
                        'line_id' => $line->id,
                        'section_id' => $section->id,
                    ]);
            }
        }

        return null;

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

        if ($issueArea->section_id) {
            $section = Section::query()->where('id', $issueArea->section_id)->first();
        }

        if ($issueArea->station_id) {
            $station = Station::query()->where('id', $issueArea->station_id)->first();
        }

        if ($section) {
            return $section->update([
                'status' => StationSectionLoopStatuses::GOOD,
            ]);
        }

        if ($station) {
            return $station->update([
                'status' => StationSectionLoopStatuses::GOOD,
            ]);
        }

        return false;
    }
}
