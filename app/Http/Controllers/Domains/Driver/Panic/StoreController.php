<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Panic;

use Carbon\Carbon;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\Panic;
use Domains\Driver\Requests\PanicRequest;
use Illuminate\Http\Response;
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

        return response(
            content: [
                'message' => 'Panic notification send successfully.',
            ],
            status: Http::OK(),
        );
    }
}
