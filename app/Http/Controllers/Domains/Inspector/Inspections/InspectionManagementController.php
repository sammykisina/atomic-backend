<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector\Inspections;

use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Requests\InspectionRequest;
use Domains\Inspector\Requests\StopInspectionRequest;
use Domains\Inspector\Resources\InspectionResource;
use Domains\Inspector\Services\InspectionService;
use Domains\SeniorTrackInspector\Enums\InspectionScheduleStatuses;
use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Http\Request;
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
            type: UserTypes::TRACK_ATTENDANT,
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
        $pre_inspection = InspectionService::getActiveInspection(inspectionSchedule: $inspection_schedule);

        if ($pre_inspection) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'You can only have one inspection a day. Check on the current active inspection actions to continue with your currently active inspection.',
            );
        }
        $inspection = $this->inspectionService->createInspection(
            inspectionData: $request->validated(),
            inspection_schedule: $inspection_schedule,
        );
        if ( ! $inspection) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection no initialized.Please try again',
            );
        }

        return Response(
            content: [
                'inspection' => new InspectionResource(
                    resource: $inspection,
                ),
                'message' => 'Inspection created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * SHOW INSPECTION
     * @param Request $request
     * @param Inspection $inspection
     * @return Response
     */
    public function show(Request $request, Inspection $inspection): Response
    {
        return Response(
            content: [
                'inspection' => new InspectionResource(
                    resource: $inspection,
                ),
                'message' => 'Inspection fetched successfully.',
            ],
            status: Http::OK(),
        );
    }

    /**
     * STOP INSPECTION
     * @param StopInspectionRequest $request
     * @param Inspection $inspection
     * @return Response | HttpException
     */
    public function stop(StopInspectionRequest $request, Inspection $inspection): Response | HttpException
    {

        if ( ! $this->inspectionService->stopInspection(
            inspection: $inspection,
            inspectionData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Inspection not stopped. Please try again",
            );
        }

        return Response(
            content: [
                'message' => 'Inspection stopped successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ABORT INSPECTION
     * @param StopInspectionRequest $request
     * @param Inspection $inspection
     * @return Response | HttpException
     */
    public function abortJourney(StopInspectionRequest $request, Inspection $inspection): Response | HttpException
    {
        if ( ! $this->inspectionService->abortInspection(
            inspection: $inspection,
            inspectionData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "Inspection not aborted. Please try again",
            );
        }

        return Response(
            content: [
                'message' => 'Inspection aborted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
