<?php

declare(strict_types=1);

namespace Domains\Inspector\Services;

use Carbon\Carbon;
use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Domains\ReginalCivilEngineer\Models\Inspection;
use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\ReginalCivilEngineer\Models\Issue;
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
            'start_time' => $inspectionData['start_time'],
            'is_active' => true,
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
        ]);
    }
}
