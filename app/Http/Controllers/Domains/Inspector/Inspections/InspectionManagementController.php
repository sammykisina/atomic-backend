<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector\Inspections;

use Domains\Inspector\Requests\InspectionRequest;
use Domains\Inspector\Services\InspectionService;
use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InspectionManagementController
{
    public function __construct(
        protected InspectionService $inspectionService,
    ) {}


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

    /**
     * CREATE INSPECTION
     * @param InspectionRequest $request
     * @return HttpException|Response
     */
    public function createInspection(InspectionRequest $request): HttpException | Response
    {
        // check if the current user is an inspector
        $inspector = EmployeeService::getEmployee(
            type: UserTypes::INSPECTOR,
            employee_id: Auth::id(),
        );

        if ( ! $inspector) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspectors can only be created by inspectors. Contact your Superior for more inquiries.',
            );
        }

        $inspection_schedule = InspectionService::getInspectionSchedule();
        if ( ! $inspection_schedule) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Your don\'t have an active inspection schedule. Please ensure any inspection schedule assigned to you is confirmed.Otherwise contact your Superior for inquires.',
            );
        }

        // check if the current user has an active inspection
        $inspection = InspectionService::getActiveInspection(inspectionSchedule: $inspection_schedule);

        if ($inspection) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'You can only have one inspection a day. Check on the current active inspection actions to continue if you are not done inspecting.',
            );
        }

        if ( ! $this->inspectionService->createInspection(
            inspectionData: $request->validated(),
            inspection_schedule: $inspection_schedule,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection no initialized.Please try again',
            );
        }

        return Response(
            content: [
                'message' => 'Inspection created. Please start your walk. Be sure to record all the issues noticed on your inspection.',
            ],
            status: Http::CREATED(),
        );
    }

}
