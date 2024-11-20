<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Domains\Driver\Models\Journey;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\AcceptLineExitRequestNotification;
use Domains\Operator\Notifications\RejectLineExitRequest;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * REJECT EXIT LINE REQUEST
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function rejectExitLineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $journey->train->driver->notify(new RejectLineExitRequest(
            journey: $journey,
            reason_for_rejection: $request->get(key: 'reason_for_rejection'),
        ));

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_REJECTED->value,
                'rejected_by' => Auth::user()->employee_id,
                'rejected_at' => now(),
                'reason_for_rejection' => $request->get(key: 'reason_for_rejection'),
            ],
        ], $journey->logs ?? []);

        $journey->update(attributes: [
            'logs' => $logs,
        ]);

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_REJECTED,
            'resourceble_id' => $journey->id,
            'resourceble_type' => get_class(object: $journey),
            'actor_id' => Auth::id(),
            'receiver_id' => $journey->train->driver_id,
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'train_id' => $journey->train_id,
            'locomotive_number_id' => $journey->train->locomotive_number_id,
            'message' => $request->get(key: 'reason_for_rejection'),
        ]);


        $notification->markAsRead();

        return response(
            content: [
                'message' => 'Exit line request rejected successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ACCEPT EXIT LINE REQUEST
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function acceptExitLineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $journey->train->driver->notify(new AcceptLineExitRequestNotification(journey: $journey));
        $notification->markAsRead();

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_ACCEPTED->value,
                'accepted_by' => Auth::user()->employee_id,
                'accepted_at' => now(),
            ],
        ], $journey->logs ?? []);

        $journey->update(attributes: [
            'logs' => $logs,
        ]);

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_ACCEPTED,
            'resourceble_id' => $journey->id,
            'resourceble_type' => get_class(object: $journey),
            'actor_id' => Auth::id(),
            'receiver_id' => $journey->train->driver_id,
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'train_id' => $journey->train_id,
            'locomotive_number_id' => $journey->train->locomotive_number_id,
        ]);

        return response(
            content: [
                'message' => 'Exit line request accepted successfully.',
            ],
            status: Http::ACCEPTED(),
        );

    }
}
