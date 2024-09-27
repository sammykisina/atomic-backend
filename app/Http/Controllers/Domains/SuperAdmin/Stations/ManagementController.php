<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Stations;

use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Requests\CreateOrEditStationRequest;
use Domains\SuperAdmin\Resources\StationResource;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected StationService $stationService,
    ) {}

    /**
     * CREATE STATION
     * @param CreateOrEditStationRequest $request
     * @return Response | HttpException
     */
    public function create(CreateOrEditStationRequest $request): HttpException | Response
    {
        $station = $this->stationService->createStation(
            stationData: $request->validated(),
        );

        if ( ! $station) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Station creation failed.',
            );
        }

        return response(
            content: [
                'station' => new StationResource(
                    resource: $station,
                ),
                'message' => 'Station created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT STATION
     * @param CreateOrEditStationRequest $request
     * @param Station $station
     * @return Response | HttpException
     */
    public function edit(CreateOrEditStationRequest $request, Station $station): Response | HttpException
    {
        if ( ! $this->stationService->editStation(
            station: $station,
            updatedStationData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Station update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Station updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

     /**
     * SHOW SECTION
     * @param Station $Station
     * @return Response
     */
    public function show(Station $station): Response | HttpException
    {
        return response(
            content: [
                'message' => 'Station fetched successfully.',
                'Station' => new StationResource(
                    resource: $station,
                ),
            ],
            status: Http::OK(),
        );
    }
}
