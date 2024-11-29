<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\TrainName;

final class TrainNameService
{
    /**
     * GET TRAIN NAME WITH ID
     * @param int $train_name_id
     * @return TrainName | null
     */
    public static function getTrainNameById(int $train_name_id): ?TrainName
    {
        return TrainName::query()
            ->where('id', $train_name_id)->first();
    }

    /**
     * CREATE TRAIN NAME
     * @param array $trainNameData
     * @return TrainName
     */
    public function createTrainName(array $trainNameData): TrainName
    {
        return TrainName::query()->create(attributes: $trainNameData);
    }

    /**
     * EDIT TRAIN NAME
     * @param array $updatedTrainNameData
     * @return bool
     */
    public function editTrainName(array $updatedTrainNameData, TrainName $trainName): bool
    {
        return $trainName->update(attributes: $updatedTrainNameData);
    }
}
