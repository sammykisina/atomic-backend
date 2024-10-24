<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Trains;

use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Requests\CreateOrEditTrainRequest;
use Domains\SuperAdmin\Resources\TrainResource;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    /** @param TrainService $trainService */
    public function __construct(
        protected TrainService $trainService,
    ) {}


    /**
     * CREATE TRAIN
     * @param CreateOrEditTrainRequest $request
     * @return Response
     */
    public function create(CreateOrEditTrainRequest $request): Response
    {
        $train = $this->trainService->createTrain(
            trainData: $request->validated(),
        );

        if ( ! $train) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train creation failed.Please try again.',
            );
        }

        return response(
            content: [
                'train' => new TrainResource(
                    resource: $train,
                ),
                'message' => 'Train created successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * EDIT TRAIN
     * @param CreateOrEditTrainRequest $request
     * @param Train $train
     * @return Response
     */
    public function edit(CreateOrEditTrainRequest $request, Train $train): Response
    {
        $edited = $this->trainService->editTrain(
            trainData: $request->validated(),
            train: $train,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train update failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Train updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW TRAIN
     * @param Train $train
     * @return Response
     */
    public function show(Train $train): Response
    {
        $train = TrainService::getTrainById(
            train_id: $train->id,
        );

        return response(
            content: [
                'message' => 'Train fetched successfully.',
                'train' => new TrainResource(
                    resource: $train,
                ),
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE TRAIN
     * @param Train $train
     * @return Response
     */
    public function delete(Train $train): Response
    {
        $deleted = $train->delete();

        if ( ! $deleted) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Train deletion failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Train deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
