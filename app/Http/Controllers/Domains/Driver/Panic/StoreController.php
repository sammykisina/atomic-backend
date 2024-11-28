<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Panic;

use Carbon\Carbon;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\Panic;
use Domains\Driver\Requests\PanicRequest;
use Domains\Driver\Services\JourneyService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class StoreController
{
    public function __invoke(PanicRequest $request, Journey $journey): Response
    {
        $shifts = $journey->shifts ?? [];
        $last_shift = end($shifts);

        $panic = Panic::query()
            ->create(attributes: [
                'journey_id' => $journey->id,
                'shift_id' => $last_shift,
                'issue' => $request->validated('issue'),
                'description' => $request->validated('description'),
                'time' => Carbon::now()->format('H:i'),
                'date' => Carbon::now(),
                'latitude' => $request->validated('latitude'),
                'longitude' => $request->validated('longitude'),
            ]);

        if ( ! $panic) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Panic not send to  operator. PLease try again.',
            );
        }

        defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::EMERGENCY_ALERT,
            'resourceble_id' => $panic->id,
            'resourceble_type' => get_class($panic),
            'actor_id' => Auth::id(),
            'receiver_id' => $panic->shift->user_id,
            'train_id' => $journey->train_id,
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'locomotive_number_id' => $journey->train->locomotive_number_id,
            'message' => $panic->issue
        ]));

        return response(
            content: [
                'message' => 'Panic notification send successfully.',
            ],
            status: Http::OK(),
        );
    }
}
