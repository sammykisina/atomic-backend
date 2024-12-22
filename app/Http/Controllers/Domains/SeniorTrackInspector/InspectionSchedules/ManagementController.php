<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorTrackInspector\InspectionSchedules;

use Domains\SeniorTrackInspector\Enums\InspectionScheduleStatuses;
use Domains\SeniorTrackInspector\Mails\InspectionScheduleMail;
use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\SeniorTrackInspector\Requests\CreateInspectionSchedulesRequest;
use Domains\SeniorTrackInspector\Requests\EditInspectionScheduleRequest;
use Domains\SeniorTrackInspector\Services\InspectionScheduleService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Services\Staff\EmployeeService;
use Domains\SuperAdmin\Services\LineService;
use Domains\SuperAdmin\Services\RegionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected InspectionScheduleService $inspectionScheduleService,
    ) {}

    /**
     * CREATE INSPECTION SCHEDULES
     * @param CreateInspectionSchedulesRequest $request
     * @return HttpException|Response
     */
    public function create(CreateInspectionSchedulesRequest $request): HttpException | Response
    {
        $inspection_schedules_created = DB::transaction(function () use ($request): bool {
            foreach ($request->validated(key: 'inspection_schedules') as $key => $inspection_schedule) {
                // check if the employee is an inspector
                $inspector = EmployeeService::getEmployee(
                    types: [UserTypes::TRACK_ATTENDANT],
                    employee_id: $inspection_schedule['inspector_id'],
                );

                if ( ! $inspector) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'The employee selected at position ' . ($key + 1) . ' may not be an inspector. Please confirm and try again.',
                    );
                }

                // check if the inspector is in the same region as the user
                if ($inspector->region_id !== Auth::user()->region_id) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'The employee selected at position ' . ($key + 1) . ' may not be within your region of operation. Please confirm and try again.',
                    );
                }

                // check if the inspector already has a schedule
                $current_inspector_schedule = $this->inspectionScheduleService::getInspectionSchedule(
                    inspector: $inspector,
                );

                if ($current_inspector_schedule) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'The employee selected at position ' . ($key + 1) . ' already has a schedule. Deactivate it to create a new one.',
                    );
                }

                // check for overlaps or gaps in kilometers
                $line = LineService::getLineWithId(
                    line_id: $request->validated(key: 'line_id'),
                );
                $region = RegionService::getRegionWithId(
                    region_id: Auth::user()->region_id,
                );

                // get existing schedules for the same line
                $existingSchedules = $this->inspectionScheduleService->getInspectionSchedulesForLine(
                    line: $line,
                    region: $region,
                );

                foreach ($existingSchedules as $existingSchedule) {
                    if (
                        $inspection_schedule['start_kilometer'] < $existingSchedule->end_kilometer &&
                            $inspection_schedule['end_kilometer'] > $existingSchedule->start_kilometer
                    ) {
                        abort(
                            code: Http::EXPECTATION_FAILED(),
                            message: 'The schedule at position ' . ($key + 1) . ' overlaps with an existing schedule. Please adjust the kilometers.',
                        );
                    }
                }

                // create the inspection schedule if no overlaps or gaps found
                $inspection_schedule = $this->inspectionScheduleService->createInspectionSchedule(
                    inspectionScheduleData: $inspection_schedule,
                    line: $line,
                    region: $region,
                );

                Mail::to($inspection_schedule->inspector)->send(
                    new InspectionScheduleMail(
                        type: NotificationTypes::INSPECTION_SCHEDULE_CREATED,
                        inspectionSchedule: $inspection_schedule,
                    ),
                );
            }

            return true;
        });

        if ( ! $inspection_schedules_created) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Something went wrong. Inspection schedules not created. Please try again',
            );
        }

        return Response(
            content: [
                'message' => 'Inspection schedules created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT INSPECTION SCHEDULE
     * @param EditInspectionScheduleRequest $request
     * @param InspectionSchedule $inspectionSchedule
     * @return HttpException|Response
     */
    public function edit(EditInspectionScheduleRequest $request, InspectionSchedule $inspectionSchedule): HttpException | Response
    {
        $line = LineService::getLineWithId(
            line_id: $inspectionSchedule->line_id,
        );
        $region = RegionService::getRegionWithId(
            region_id: Auth::user()->region_id,
        );

        $existingSchedules = $this->inspectionScheduleService->getInspectionSchedulesForLine(
            line: $line,
            region: $region,
        )->filter(fn(InspectionSchedule $schedule) => $schedule->id !== $inspectionSchedule->id);



        foreach ($existingSchedules as $existingSchedule) {
            if (
                $request->validated('start_kilometer') < $existingSchedule->end_kilometer &&
                    $request->validated('end_kilometer') > $existingSchedule->start_kilometer
            ) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'The schedule overlaps with an existing schedule. Please adjust the kilometers.',
                );
            }
        }

        if ( ! $this->inspectionScheduleService->editInspectionSchedule(
            inspectionSchedule: $inspectionSchedule,
            updatedInspectionScheduleData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection schedule not updated.',
            );
        }

        return Response(
            content: [
                'message' => 'Inspection schedules updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE INSPECTION SCHEDULE
     * @param InspectionSchedule $inspectionSchedule
     * @return HttpException|Response
     */
    public function delete(InspectionSchedule $inspectionSchedule): HttpException | Response
    {
        if (InspectionScheduleStatuses::ACTIVE === $inspectionSchedule->status) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection schedule is still active.Deactivate it then delete',
            );
        }

        if ( ! $inspectionSchedule->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Inspection schedule not deleted.Please try again.',
            );
        }

        return Response(
            content: [
                'message' => 'Inspection schedules deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
