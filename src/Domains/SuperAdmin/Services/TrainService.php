<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Train;

final class TrainService
{
    /**
     * GET TRAIN WITH ID
     * @param int $train_id
     * @return Train | null
     */
    public static function getTrainById(int $train_id): ?Train
    {
        return Train::query()
            ->with(relations: [
                'origin',
                'destination',
                'driver',
                'locomotiveNumber',
            ])->where('id', $train_id)->first();
    }

    /**
     * CREATE TRAIN
     * @param array $trainData
     * @return Train
     */
    public function createTrain(array $trainData): Train
    {
        return Train::query()->create(attributes: $trainData);
    }

    /**
     * EDIT TRAIN
     * @param array $trainData
     * @return Train
     */
    public function editTrain(array $trainData, Train $train): bool
    {
        return $train->update(attributes: $trainData);
    }
}
