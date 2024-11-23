<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator;

use Domains\Operator\Requests\AddOrRemoveInterdictionRequest;
use Domains\Operator\Services\LicenseService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class LineManagement
{
    /**
     * ADD INTERDICTION
     * @param AddOrRemoveInterdictionRequest $request
     * @return Response
     */
    public function addInterdiction(AddOrRemoveInterdictionRequest $request): Response
    {
        $model = LicenseService::getModel(
            model_type: $request->validated('type'),
            model_id: $request->validated('id'),
        );

        if ( ! $model) {
            abort(
                code: Http::NOT_FOUND(),
                message: 'Area not found',
            );
        }

        if ( ! $model->update(attributes: [
            'status' => StationSectionLoopStatuses::INTERDICTION->value,
        ])) {
            abort(
                code: Http::NOT_IMPLEMENTED(),
                message: 'Interdiction not added. Please try again',
            );
        }

        return response(
            content: [
                'message' => 'Interdiction added successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * REMOVE INTERDICTION
     * @param AddOrRemoveInterdictionRequest $request
     * @return Response
     */
    public function removeInterdiction(AddOrRemoveInterdictionRequest $request): Response
    {
        $model = LicenseService::getModel(
            model_type: $request->validated('type'),
            model_id: $request->validated('id'),
        );

        if ( ! $model) {
            abort(
                code: Http::NOT_FOUND(),
                message: 'Area not found',
            );
        }

        if ( ! $model->update(attributes: [
            'status' => StationSectionLoopStatuses::GOOD->value,
        ])) {
            abort(
                code: Http::NOT_IMPLEMENTED(),
                message: 'Interdiction not removed. Please try again',
            );
        }

        return response(
            content: [
                'message' => 'Interdiction removed successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
