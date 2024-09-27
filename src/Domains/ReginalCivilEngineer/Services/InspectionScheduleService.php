<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Services;

use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Region;
use Illuminate\Database\Eloquent\Collection;

final class InspectionScheduleService
{
    /**
     * GET ACTIVE INSPECTOR SCHEDULE
     * @param User $inspector
     * @return InspectionSchedule | null
     */
    public static function getInspectionSchedule(User $inspector): ?InspectionSchedule
    {
        return InspectionSchedule::query()
            ->where('inspector_id', $inspector->id)
            ->whereIn(column: 'status', values: [InspectionScheduleStatuses::ACTIVE, InspectionScheduleStatuses::AWAITING_CONFIRMATION])
            ->first();
    }
    /**
     * CREATE INSPECTION SCHEDULE
     * @param array $inspectionScheduleDate
     * @return InspectionSchedule
     */
    public function createInspectionSchedule(array $inspectionScheduleData, Line $line, Region $region): InspectionSchedule
    {
        return InspectionSchedule::query()->create([
            'inspector_id' => $inspectionScheduleData['inspector_id'],
            'time' => $inspectionScheduleData['time'],
            'line_id' => $line->id,
            'region_id' => $region->id,
            'start_kilometer' => $inspectionScheduleData['start_kilometer'],
            'end_kilometer' => $inspectionScheduleData['end_kilometer'],
        ]);

    }

    /**
     * GET LINE CURRENT INSPECTION SCHEDULES
     * @param Line $line
     * @param Region $region
     * @return Collection
     */
    public function getInspectionSchedulesForLine(Line $line, Region $region): ?Collection
    {
        return InspectionSchedule::query()
            ->where('line_id', $line->id)
            ->where('region_id', $region->id)
            ->whereIn(column: 'status', values: [InspectionScheduleStatuses::ACTIVE, InspectionScheduleStatuses::AWAITING_CONFIRMATION])
            ->get();
    }

    /**
     * EDIT INSPECTION SCHEDULE
     * @param InspectionSchedule $inspectionSchedule
     * @param array $updatedInspectionScheduleData
     * @return bool
     */
    public function editInspectionSchedule(InspectionSchedule $inspectionSchedule, array $updatedInspectionScheduleData): bool
    {
        return $inspectionSchedule->update([
            'time' => $updatedInspectionScheduleData['time'],
            'status' => $updatedInspectionScheduleData['status'],
            'start_kilometer' => $updatedInspectionScheduleData['start_kilometer'],
            'end_kilometer' => $updatedInspectionScheduleData['end_kilometer'],
        ]);

    }
}
