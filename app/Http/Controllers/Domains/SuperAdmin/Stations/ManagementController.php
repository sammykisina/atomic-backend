<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Stations;

use Domains\SuperAdmin\Exports\StationsExport;
use Domains\SuperAdmin\Imports\StationsImport;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Requests\CreateOrEditStationRequest;
use Domains\SuperAdmin\Resources\StationResource;
use Domains\SuperAdmin\Services\LineService;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        private StationService $stationService,
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

    /**
     * EXPORT STATIONS TEMPLATE
     * @param Excel $excel
     * @return BinaryFileResponse
     */
    public function exportStations(Excel $excel): BinaryFileResponse
    {
        return $excel->download(export: new StationsExport(), fileName: 'stations.xlsx');
    }

    /**
     * IMPORT STATIONS
     * @param Request $request
     * @return void
     */
    public function importStations(Request $request): Response
    {
        $import = new StationsImport(
            stationService: $this->stationService,
            line_id: (int) $request->get('line_id'),
        );

        $import->import($request->file('stations'));

        if ($import->errors()->isNotEmpty()) {
            return response(
                content: [
                    'message' => 'Please check the following issues in your spread sheet',
                    'errors' => $import->errors(),
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'Stations uploaded successfully',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * DELETE STATION
     * @param Station $line
     * @return Response|HttpException
     */
    public function delete(Station $station): Response | HttpException
    {
        $line = LineService::getLineWithId(
            line_id: $station->line_id,
        );

        // Check if this is the last station in its line
        if ($line->stations()->count() > 1) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'Cannot delete station as it is not the last station in its line.',
            );
        }

        // Ensure the station has no associated loops
        if ($station->loops()->exists()) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'Cannot delete station as it has associated loops.',
            );
        }

        // Ensure the station has no associated sections
        if ($station->section) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'Cannot delete station as it has associated section. Delete the section first.',
            );
        }

        // Attempt to delete the station
        if ( ! $station->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Station deletion failed.',
            );
        }

        return response(
            content: [
                'message' => 'Station deleted successfully.',
            ],
            status: Http::OK(),
        );
    }

}
