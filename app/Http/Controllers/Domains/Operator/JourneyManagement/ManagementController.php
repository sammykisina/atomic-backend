<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\JourneyManagement;

use Domains\Driver\Models\Journey;
use Domains\Operator\Notifications\DriverCurrentLocationNotification;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function confirmDriverCurrentLocation(Request $request, Journey $journey): Response | HttpException
    {
        $result = DB::transaction((function () use ($request, $journey): bool {
            $notification  = auth()->user()
                ->unreadNotifications
                ->where('id', $request->notification_id)
                ->first();

            $operator = User::query()->where('type', UserTypes::OPERATOR_CONTROLLER->value)->first();

            if ($notification) {
                $notification->markAsRead();
            }

            $driver = $journey->driver;
            $driver->notify(new DriverCurrentLocationNotification(
                operator: $operator,
            ));

            return true;
        }));

        if ( ! $result) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Something went wrong. Send confirmation request again.',
            );
        }

        return response(
            content: [
                'message' => 'The driver is notified of your request.You will be notified when the driver responds.',
            ],
            status: Http::CREATED(),
        );
    }

    public function confirmJourney(Journey $journey): void
    {
        // check if we know of the current location of the driver and notify them if we




    }
}
