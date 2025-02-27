<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Stations;

use App\Actions\Superadmin\Stations\MakeStationTriplineAware;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class MakeStationTripLineAwareController
{
    public function __invoke(
        Request $request,
        string $station,
        MakeStationTriplineAware $makeStationTriplineAware,
    ): Response | HttpException {
        $station = Station::query()->where('id', $station)->with('section')->firstOrFail();
        if ( ! $makeStationTriplineAware->handle(station: $station)) {
            abort(code: 417, message: 'Failed to make station trip-line aware');
        }

        return response(content: ['message' => 'Station made trip-line aware'], status: 200);
    }
}
