<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Domains\Driver\Models\Journey;
use Domains\Operator\Notifications\AcceptLineExitRequestNotification;
use Domains\Operator\Notifications\RejectLineExitRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
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


        $journey->train->driver->notify(new RejectLineExitRequest(journey: $journey));
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

        return response(
            content: [
                'message' => 'Exit line request accepted successfully.',
            ],
            status: Http::ACCEPTED(),
        );

    }
}
