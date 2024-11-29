<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\TrainNames;

use Domains\SuperAdmin\Models\TrainName;
use Domains\SuperAdmin\Resources\TrainNameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $train_names  = QueryBuilder::for(subject: TrainName::class)
            ->get();

        return response(
            content: [
                'message' => 'Train names fetched successfully.',
                'train_names' => TrainNameResource::collection(
                    resource: $train_names,
                ),
            ],
            status: Http::OK(),
        );
    }
}
