<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\OperatorExitLineRequestNotification;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Models\User;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;

final class ExitTrainController
{
    /**
     * SEND EXIT TRAIN CONFIRMATION NOTIFICATION
     * @param Request $request
     * @param Journey $journey
     * @return Response
     */
    public function requestLineExit(Request $request, Journey $journey): Response
    {
        $operator = User::query()->where('id', Auth::id())->first();
        $operator->notify(new OperatorExitLineRequestNotification(
            journey: $journey,
        ));

        defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::OPERATOR_REQUEST_LINE_EXIT,
            'resourceble_id' => $journey->id,
            'resourceble_type' => get_class(object: $journey),
            'actor_id' => Auth::id(),
            'receiver_id' => Auth::id(),
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'train_id' => $journey->train_id,
            'locomotive_number_id' => $journey->train->locomotive_number_id,
            'message' => $request->get(key: 'reason_for_exit'),
        ]));

        return response(
            content: [
                'message' => 'Line exit request sent successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * SEND EXIT TRAIN CONFIRMATION NOTIFICATION
     * @param Request $request
     * @param Journey $journey
     * @return Response
     */
    public function lineExit(Request $request, Journey $journey, DatabaseNotification $notification): Response
    {
        $exited =  DB::transaction(function () use ($journey, $notification): bool {
            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
                );
            }

            if ( ! $journey->update(attributes: [
                'is_active' => false,
                'last_destination' => JourneyService::getTrainLocation(
                    journey: $journey,
                ) ?? $journey->requesting_location,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Train not exited.Please try again.',
                );
            }

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::OPERATOR_MACRO10->value,
                    'line_exited_by' => Auth::user()->employee_id,
                    'line_exited_at' => now(),
                ],
            ], $journey->logs ?? []);

            $journey->update(attributes: [
                'logs' => $logs,
            ]);

            if ( ! $journey->train->trainName->update([
                'is_active' => false,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This train was not deactivated for this trip.Please try again.',
                );
            }

            $prev_latest_license = LicenseService::getPrevLatestLicense(
                journey: $journey,
            );

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::LICENSE_USED->value,
                    'marked_as_used_by' => Auth::user()->employee_id,
                    'marked_at' => now(),
                ],
            ], $prev_latest_license->logs ?? []);

            if ($prev_latest_license) {
                $prev_latest_license->update(attributes: [
                    'status' => LicenseStatuses::USED->value,
                    'logs' => $logs,
                ]);
            }

            $notification->markAsRead();

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::OPERATOR_MACRO10,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]));

            return true;
        });

        if ( ! $exited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train not exited. Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Train exited  successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
