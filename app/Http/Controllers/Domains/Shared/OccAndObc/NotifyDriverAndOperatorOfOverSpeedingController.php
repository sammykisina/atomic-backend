<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\OccAndObc;

use Domains\Driver\Models\Journey;
use Domains\Driver\Notifications\DriverOverSpeedingNotification;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\OperatorOverSpeedingNotification;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;

final class NotifyDriverAndOperatorOfOverSpeedingController
{
    public function __invoke(Request $request, Journey $journey): Response
    {
        $notified =  DB::transaction(function () use ($journey): bool {

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            $current_location = JourneyService::getTrainLocation(journey: $journey);

            $logs = array_merge([
                ['type' => AtomikLogsTypes::DRIVER_OVER_SPEEDING->value,
                    'notified_by' => Auth::user()->employee_id,
                    'notified_at' => now(),
                    'current_location' => $current_location ? $current_location['name'] : $journey->requesting_location['name'] ,
                ],
            ], $journey->logs);

            $journey->update(attributes: [
                'logs' => $logs,
            ]);


            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::DRIVER_OVER_SPEEDING,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' =>  $shift->user->id,
                'current_location' => $current_location ? $current_location['name'] : $journey->requesting_location['name'] ,
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]);

            $shift->user->notify(new OperatorOverSpeedingNotification(
                journey: $journey,
            ));

            $journey->train->driver->notify(new DriverOverSpeedingNotification());

            return true;
        });

        if ( ! $notified) {
            return response(
                content: [
                    'message' => 'Something went wrong.',
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'Operator has been notified of your over-speeding.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
