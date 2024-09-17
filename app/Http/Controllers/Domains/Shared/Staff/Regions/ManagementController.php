<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Regions;

use Domains\Shared\Models\Region;
use Domains\Shared\Resources\RegionResource;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    /**
     * REGION SHOW
     * @param Region $employee
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
}
