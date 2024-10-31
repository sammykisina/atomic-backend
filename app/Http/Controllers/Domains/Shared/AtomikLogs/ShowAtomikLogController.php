<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\AtomikLogs;

use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Resources\LicenseResource;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Models\AtomikLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ShowAtomikLogController
{
    public function __invoke(Request $request, AtomikLog $atomikLog): Response
    {
        $log = match ($atomikLog->resourceble_type) {
            Journey::class => new JourneyResource(
                resource: JourneyService::getJourneyById(
                    journey_id: $atomikLog->resourceble_id,
                ),
            ),
            License::class => new LicenseResource(
                resource: LicenseService::getLicenseById(
                    license_id: $atomikLog->resourceble_id,
                ),
            ),
        };


        return response(
            content: [
                'message' => 'Atomik log fetched successfully.',
                'atomik_log' => $log,
            ],
            status: Http::OK(),
        );
    }

}
