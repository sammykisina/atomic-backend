<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Shifts;

use Domains\Operator\Enums\ShiftStatuses;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * SHIFT CONFIRMATION
     * @param Shift $shift
     * @param DatabaseNotification $notification
     * @return HttpException|Response
     */
    public function acceptShift(Shift $shift, DatabaseNotification $notification): HttpException | Response
    {
        if ( ! $shift->update([
            'status' => ShiftStatuses::CONFIRMED,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift confirmation failed.',
            );
        }

        $notification->markAsRead();

        return Response(
            content: [
                'message' => 'Shift confirmation successful.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * MANUAL SHIFT ACTIVATION
     * @param Shift $shift
     * @return HttpException|Response
     */
    public function manualShiftActivation(Shift $shift): HttpException | Response
    {
        if ( ! $shift->update([
            'active' => true,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Shift activation failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Shift activated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
