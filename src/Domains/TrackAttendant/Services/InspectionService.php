<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Services;

use Carbon\Carbon;
use Domains\SeniorTrackInspector\Enums\InspectionScheduleStatuses;
use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\SeniorTrackInspector\Models\IssueArea;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Domains\TrackAttendant\Models\Inspection;
use Domains\TrackAttendant\Models\Issue;
use Illuminate\Support\Facades\Auth;

final class InspectionService
{
    /**
     * GET INSPECTION SCHEDULE
     * @return InspectionSchedule|null
     */
    public static function getInspectionSchedule(): ?InspectionSchedule
    {
        return InspectionSchedule::query()
            ->where('inspector_id', Auth::id())
            ->where('status', InspectionScheduleStatuses::ACTIVE)
            ->first();
    }

    /**
     * GET CURRENT INSPECTOR ACTIVE INSPECTION
     * @param InspectionSchedule $inspectionSchedule
     * @return Inspection|null
     */
    public static function getActiveInspection(InspectionSchedule $inspectionSchedule): ?Inspection
    {
        return Inspection::query()
            ->where('inspection_schedule_id', $inspectionSchedule->id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * CREATE INSPECTION
     * @param array $inspectionData
     * @return Inspection
     */
    public function createInspection(array $inspectionData, InspectionSchedule $inspection_schedule): Inspection
    {
        return Inspection::query()->create([
            'inspection_schedule_id' => $inspection_schedule->id,
            'date' => Carbon::now(),
            'start_time' => Carbon::now()->format('H:i'),
            'is_active' => true,
            'inspector_reached_origin' => false,
            'inspector_reached_destination' => false,
        ]);
    }

    /**
     * STOP INSPECTION
     * @return Inspection
     */
    public function stopInspection(Inspection $inspection, array $inspectionData): bool
    {
        return $inspection->update([
            'end_time' => Carbon::now()->format('H:i'),
            'is_active' => false,
            'inspector_reached_origin' => $inspectionData['inspector_reached_origin'],
            'inspector_reached_destination' => true,
        ]);
    }

    /**
     * ABORT INSPECTION
     * @return Inspection
     */
    public function abortInspection(Inspection $inspection, array $inspectionData): bool
    {
        return $inspection->update([
            'aborted_time' => Carbon::now()->format('H:i'),
            'is_active' => false,
            'inspector_reached_origin' => $inspectionData['inspector_reached_origin'],
            'inspector_reached_destination' => $inspectionData['inspector_reached_destination'],
            'reason_for_abortion' => $inspectionData['reason_for_abortion'],
        ]);
    }

    /**
     * CREATE INSPECTION ISSUE
     * @param Inspection $inspection
     * @param array $inspectionIssueData
     * @return Issue
     */
    public function createInspectionIssue(Inspection $inspection, array $inspectionIssueData): Issue
    {
        return Issue::query()->create(array_merge([
            'inspection_id' => $inspection->id,
        ], $inspectionIssueData));
    }


    /**
     * CLOSE SECTION OR STATION DUE TO A CRITICAL ISSUE
     * @param IssueArea $issueArea
     * @return bool
     */
    public function closeSectionOrStation(IssueArea $issueArea): bool
    {
        $station = null;
        $section = null;

        if ($issueArea->station_id) {
            $station = StationService::getStationById(
                station_id: $issueArea->station_id,
            );

            return $station->update(attributes: [
                'status' => StationSectionLoopStatuses::INTERDICTION->value,
            ]);
        }

        if ($issueArea->section_id) {
            $section = SectionService::getSectionById(
                section_id: $issueArea->section_id,
            );

            return $section->update(attributes: [
                'status' => StationSectionLoopStatuses::INTERDICTION->value,
            ]);
        }

        return false;
    }
}
