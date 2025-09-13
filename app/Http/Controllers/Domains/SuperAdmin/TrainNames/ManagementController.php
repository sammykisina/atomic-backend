<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\TrainNames;

use Domains\SuperAdmin\Models\TrainName;
use Domains\SuperAdmin\Requests\CreateOrEditTrainNameRequest;
use Domains\SuperAdmin\Resources\TrainNameResource;
use Domains\SuperAdmin\Services\TrainNameService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    /** @param TrainNameService $trainService */
    public function __construct(
        private TrainNameService $trainNameService,
    ) {}


    /**
     * CREATE TRAIN NAME
     * @param CreateOrEditTrainNameRequest $request
     * @return Response
     */
    public function create(CreateOrEditTrainNameRequest $request): Response
    {
        $train_name = $this->trainNameService->createTrainName(
            trainNameData: $request->validated(),
        );

        if ( ! $train_name) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train name creation failed.Please try again.',
            );
        }

        return response(
            content: [
                'train' => new TrainNameResource(
                    resource: $train_name,
                ),
                'message' => 'Train created successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * EDIT TRAIN NAME
     * @param CreateOrEditTrainNameRequest $request
     * @param TrainName $train
     * @return Response
     */
    public function edit(CreateOrEditTrainNameRequest $request, TrainName $trainName): Response
    {
        $edited = $this->trainNameService->editTrainName(
            updatedTrainNameData: $request->validated(),
            trainName: $trainName,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train name update failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Train name updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW TRAIN NAME
     * @param TrainName $train
     * @return Response
     */
    public function show(TrainName $trainName): Response
    {
        $train_name = TrainNameService::getTrainNameById(
            train_name_id: $trainName->id,
        );

        return response(
            content: [
                'message' => 'Train name fetched successfully.',
                'train_name' => new TrainNameResource(
                    resource: $train_name,
                ),
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE TRAIN NAME
     * @param TrainName $train
     * @return Response
     */
    public function delete(TrainName $trainName): Response
    {
        $deleted = $trainName->delete();

        if ( ! $deleted) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train name deletion failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Train name deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
