<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Requests\ClearRequest;
use Domains\Operator\Services\LicenseService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ClearLicenseAreaController
{
    public function __invoke(ClearRequest $request): Response
    {
        $model = LicenseService::getModel(
            model_type: $request->validated('type'),
            model_id: $request->validated('area_id'),
        );

        if ( ! $model) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'No model found. Please try again',
            );
        }

        if ( ! $model->update(attributes: [
            'status' => StationSectionLoopStatuses::GOOD->value,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Area not cleared. Please try again',
            );
        }


        return response(
            content: [
                'message' => 'Area cleared successfully.',
                'area' => $model,
            ],
            status: Http::ACCEPTED(),
        );
    }
}
