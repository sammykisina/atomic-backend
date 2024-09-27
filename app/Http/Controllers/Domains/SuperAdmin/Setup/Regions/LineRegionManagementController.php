<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Setup\Regions;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Requests\Setup\LineRegionDivideRequest;
use Domains\SuperAdmin\Services\LineRegionService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class LineRegionManagementController
{
    public function __construct(
        protected LineRegionService $lineRegionService,
    ) {}

    public function lineRegionsDivisions(LineRegionDivideRequest $request, Line $line): HttpException | Response
    {
        $stations = Station::query()
            ->where('line_id', $line->id)
            ->orderBy('id')->get();

        $station_ids = $stations->pluck('id')->toArray();
        $region_divisions = $request->validated('region_divisions');

        if ($region_divisions[0]['start_station_id'] !== $station_ids[0]) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The arrangement of the divisions may not be correct. Ensure that the divisions are arranged in ascending order.',
            );
        }

        foreach ($region_divisions as $index => $region_division) {
            if ($region_division['start_station_id'] >= $region_division['end_station_id']) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: "The start_station_id must be less than the end_station_id within region {$index}.",
                );
            }

            // ensure regions are sequential (no gaps between regions)
            if ($index > 0) {
                $previous_region = $region_divisions[$index - 1];
                if ($region_division['start_station_id'] !== $previous_region['end_station_id']) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: "Region {$index} start_station_id must match the previous region's end_station_id.",
                    );
                }
            }
        }

        if ( ! $this->lineRegionService->createLineRegionsDivisions(
            lineRegionsData: $request->validated('region_divisions'),
            line: $line,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Line region division failed.',
            );
        }


        return Response(
            content: [
                'message' => 'Line regions added successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
