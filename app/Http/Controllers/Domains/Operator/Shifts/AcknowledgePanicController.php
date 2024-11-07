<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Shifts;


use Carbon\Carbon;
use Domains\Driver\Models\Panic;
use Domains\Driver\Notifications\PanicAcknowledgedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class AcknowledgePanicController
{
    public function __invoke(Request $request, Panic $panic): Response
    {
        if ( ! $panic->update(attributes: [
            'is_acknowledge' => true,
            'time_of_acknowledge' => Carbon::now()->format('H:i'),
            'date_of_acknowledge' => Carbon::now(),
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Panic not acknowledged. PLease try again.',
            );
        }

        $panic->journey->train->driver->notify(new PanicAcknowledgedNotification(panic: $panic));

        return response(
            content: [
                'message' => 'Panic acknowledged successfully.',
            ],
            status: Http::OK(),
        );
    }
}
