<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\ShiftManagement\Shifts;

use Domains\Operator\Enums\ShiftActivities;
use Domains\Operator\Mails\ShiftMail;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Domains\Shared\Services\Staff\EmployeeService;
use Domains\SuperAdmin\Enums\ShiftNotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Notifications\ShiftNotification;
use Domains\SuperAdmin\Requests\ShiftManagement\CreateOrEditShiftRequest;
use Domains\SuperAdmin\Resources\ShiftManagement\ShiftResource;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected ShiftService $shiftService,
    ) {}

    /**
     * CREATE SHIFT
     * @param CreateOrEditShiftRequest $request
     * @return HttpException | Response
     */
    public function create(CreateOrEditShiftRequest $request): HttpException | Response
    {
        $operator = EmployeeService::getEmployee(
            types: [UserTypes::OPERATOR_CONTROLLER, UserTypes::OPERATOR_CONTROLLER_SUPERVISOR],
            employee_id: $request->validated(
                key: 'user_id',
            ),
        );

        if ( ! $operator) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Your are trying to create a shift to an employee who is not an operator controller.Please confirm the selected employee and try again.',
            );
        }

        if (WorkStatuses::ON_LEAVE === $operator->work_status) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This employee is currently on leave.',
            );
        }

        $validatedData = $request->validated();
        $overlap = Shift::where('desk_id', $validatedData['desk_id'])
            ->where('day', $validatedData['day'])
            ->where(function ($query) use ($validatedData): void {
                $query->where(function ($query) use ($validatedData): void {
                    $query->where('from', '<', $validatedData['to'])
                        ->where('to', '>', $validatedData['from']);
                });
            })
            ->exists();


        if ($overlap) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The shift overlaps with an existing shift or the shift already exists.Please double check shift details.',
            );
        }

        $shift = $this->shiftService->createShift(
            shiftData: $validatedData,
        );

        if ( ! $shift) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift creation failed.',
            );
        }

        defer(
            callback: fn() => Mail::to(
                $shift->user,
            )->send(
                new ShiftMail(
                    type: ShiftActivities::CREATED,
                    shift: $shift,
                ),
            ),
        );


        $shift->user->notify(new ShiftNotification(
            shift: $shift,
            type: ShiftNotificationTypes::SHIFT_CREATED,
        ));

        return response(
            content: [
                'shift' => new ShiftResource(
                    resource: $shift,
                ),
                'message' => 'Shift created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT SHIFT
     * @param CreateOrEditShiftRequest $request
     * @return HttpException | Response
     */
    public function edit(CreateOrEditShiftRequest $request, Shift $shift): HttpException | Response
    {
        $operator = EmployeeService::getEmployee(
            types: [UserTypes::OPERATOR_CONTROLLER, UserTypes::OPERATOR_CONTROLLER_SUPERVISOR],
            employee_id: $request->validated(
                key: 'user_id',
            ),
        );

        if ( ! $operator) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Your are trying to create a shift to an employee who is not an operator controller.Please confirm the selected employee and try again.',
            );
        }

        if (WorkStatuses::ON_LEAVE === $operator->work_status) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This employee is currently on leave.',
            );
        }

        $validatedData = $request->validated();
        $overlap = Shift::where('desk_id', $validatedData['desk_id'])
            ->where('day', $validatedData['day'])
            ->where('id', '!=', $shift->id)
            ->where(function ($query) use ($validatedData): void {
                $query->where('from', '<', $validatedData['to'])
                    ->where('to', '>', $validatedData['from']);
            })
            ->exists();

        if ($overlap) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The shift overlaps with an existing shift.Please double check shift details.',
            );
        }

        $edited = $this->shiftService->editShift(
            shiftData: $validatedData,
            shift: $shift,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift update failed. Please try again.',
            );
        }

        $old_shift = $shift;
        $new_shift = $shift->refresh();

        if ($new_shift->user->id === $old_shift->user->id) {
            defer(
                callback: fn() => Mail::to(
                    $old_shift->user,
                )->send(
                    new ShiftMail(
                        type: ShiftActivities::EDITED,
                        shift: $old_shift,
                    ),
                ),
            );
        } else {
            defer(
                callback: fn() => Mail::to(
                    $new_shift->user,
                )->send(
                    new ShiftMail(
                        type: ShiftActivities::CREATED,
                        shift: $new_shift,
                    ),
                ),
            );

            defer(
                callback: fn() => Mail::to(
                    $old_shift->user,
                )->send(
                    new ShiftMail(
                        type: ShiftActivities::DELETED,
                        shift: $old_shift,
                    ),
                ),
            );
        }

        return response(
            content: [
                'message' => 'Shift updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE SHIFT
     * @param Shift $shift
     * @return HttpException | Response
     */
    public function delete(Shift $shift): HttpException | Response
    {
        if ( ! $shift->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift deletion failed.Please try again.',
            );
        }

        defer(callback: fn() => Mail::to(
            $shift->user,
        )->send(
            new ShiftMail(
                type: ShiftActivities::DELETED,
                shift: $shift,
            ),
        ));


        return response(
            content: [
                'message' => 'Shift deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW SHIFT
     * @param Shift $shift
     * @return Response
     */
    public function show(Shift $shift): Response
    {
        $shift = ShiftService::getShiftById(
            shift_id: $shift->id,
        );

        return response(
            content: [
                'message' => 'Shift fetched successfully.',
                'shift' => new ShiftResource(
                    resource: $shift,
                ),
            ],
            status: Http::OK(),
        );
    }


    /**
     * DEACTIVATE SHIFT
     * @param Shift $shift
     * @return Response
     */
    public function deactivate(Shift $shift): Response
    {
        if ( ! $shift->update(attributes: [
            'active' => false,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift deactivation failed.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Shift deactivated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

}
