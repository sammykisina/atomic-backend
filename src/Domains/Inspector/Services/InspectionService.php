<?php

declare(strict_types=1);

namespace Domains\Inspector\Services;

use Carbon\Carbon;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Models\Issue;
use Domains\PermanentWayInspector\Enums\InspectionScheduleStatuses;
use Domains\PermanentWayInspector\Models\InspectionSchedule;
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
        return Issue::query()->create([
            'inspection_id' => $inspection->id,
            'description' => $inspectionIssueData['description'],
            'condition' => $inspectionIssueData['condition'],
            'latitude' => $inspectionIssueData['latitude'],
            'longitude' => $inspectionIssueData['longitude'],
            'image_url' => $inspectionIssueData['image_url'],
            'issue_name_id' => $inspectionIssueData['issue_name_id'],
            'issue_kilometer' => $inspectionIssueData['issue_kilometer'],
        ]);
    }
}
