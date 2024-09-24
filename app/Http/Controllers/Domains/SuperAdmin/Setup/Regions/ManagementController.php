<?php

declare(strict_types=1);


namespace App\Http\Controllers\Domains\SuperAdmin\Setup\Regions;


use Domains\RegionAdmin\Requests\CreateOrEditRegionRequest;
use Domains\RegionAdmin\Resources\RegionResource;
use Domains\RegionAdmin\Services\RegionService;
use Domains\SuperAdmin\Models\Region;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected RegionService $regionService,
    ) {}


    /**
     * CREATE REGION
     * @param CreateOrEditRegionRequest $request
     * @return HttpException | Response
     */
    public function create(CreateOrEditRegionRequest $request): HttpException | Response
    {
        $region = $this->regionService->createRegion(
            regionData: $request->validated(),
        );

        if ( ! $region) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Region creation failed.',
            );
        }

        return Response(
            content: [
                'region' => new RegionResource(
                    resource: $region,
                ),
                'message' => 'Region created successful.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT REGION
     * @param CreateOrEditRegionRequest $request
     * @return HttpException | Response
     */
    public function edit(CreateOrEditRegionRequest $request, Region $region): HttpException | Response
    {
        if ( ! $this->regionService->editRegion(
            updatedRegionData: $request->validated(),
            region: $region,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Region creation failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Region created successful.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * REGION SHOW
     * @param Region $region
     * @return Response
     */
    public function show(Region $region): Response
    {
        return response(
            content: [
                'message' => 'Region fetched successfully.',
                'region' => new RegionResource(
                    resource: $region,
                ),
            ],
            status: Http::OK(),
        );
    }

    /**
     * DELETE REGION
     * @param Region $region
     * @return HttpException|Response
     */
    public function delete(Region $region): HttpException | Response
    {
        if ( ! $region->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Region deletion failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Region deleted successful.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
