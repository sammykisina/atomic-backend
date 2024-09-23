<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Shifts;

use Domains\Operator\Enums\ShiftActivities;
use Domains\Operator\Mails\ShiftMail;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Domains\Shared\Services\Staff\EmployeeService;
use Domains\SuperAdmin\Enums\ShiftNotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Notifications\ShiftNotification;
use Domains\SuperAdmin\Requests\CreateOrEditShiftRequest;
use Domains\SuperAdmin\Resources\ShiftResource;
use Domains\SuperAdmin\Services\ShiftService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
            type: UserTypes::OPERATOR_CONTROLLER,
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
            ->where('line_id', $validatedData['line_id']) // Ensure same line
            ->where('day', $validatedData['day']) // Same day
            ->where(function ($query) use ($validatedData): void {
                // Check if the shifts overlap in terms of time, stations, and exclude adjacent shifts
                $query->where(function ($query) use ($validatedData): void {
                    $query->where('from', '<', $validatedData['to'])
                        ->where('to', '>', $validatedData['from']);
                });
            })
        // Add condition to check for overlapping station ranges
            ->where(function ($query) use ($validatedData): void {
                $query->where('shift_start_station_id', '<=', $validatedData['shift_end_station_id'])
                    ->where('shift_end_station_id', '>=', $validatedData['shift_start_station_id']);
            })
            ->exists();


        if ($overlap) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The shift overlaps with an existing shift or the shift already exists.Please double check shift details.',
            );
        }

        $stations = DB::table('stations')
            ->where('line_id', $validatedData['line_id'])
            ->whereBetween('id', [$validatedData['shift_start_station_id'], $validatedData['shift_end_station_id']])
            ->orderBy('id')
            ->pluck('id')->toArray();

        $shift = $this->shiftService->createShift(
            shiftData: $validatedData,
            stations: $stations,
        );

        if ( ! $shift) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift creation failed.',
            );
        }

        Mail::to(
            $shift->user,
        )->send(
            new ShiftMail(
                type: ShiftActivities::CREATED,
                shift: $shift,
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
     * DELETE SHIFT
     * @param Shift $shift
     * @return HttpException | Response
     */
    public function delete(Shift $shift): HttpException | Response
    {
        if ( ! $shift->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift deletion failed.',
            );
        }

        Mail::to(
            $shift->user,
        )->send(
            new ShiftMail(
                type: ShiftActivities::DELETED,
                shift: $shift,
            ),
        );

        return response(
            content: [
                'message' => 'Shift deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

}
