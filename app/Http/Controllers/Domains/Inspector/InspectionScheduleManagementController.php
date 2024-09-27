<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector;

use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InspectionScheduleManagementController
{
    /**
     * CONFIRM INSPECTION SCHEDULE
     * @param InspectionSchedule $inspectionSchedule
     * @param DatabaseNotification $databaseNotification
     * @return HttpException|Response
     */
    public function confirmInspectionSchedule(InspectionSchedule $inspectionSchedule, DatabaseNotification $notification): HttpException | Response
    {
        if ( ! $inspectionSchedule->update([
            'status' => InspectionScheduleStatuses::ACTIVE,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection Schedule not confirmed.Please try again',
            );
        }

        $notification->markAsRead();

        return Response(
            content: [
                'message' => 'Inspection Schedule confirmed successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
