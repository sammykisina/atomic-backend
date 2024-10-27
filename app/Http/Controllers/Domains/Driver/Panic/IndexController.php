<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Panic;

use Domains\Driver\Models\Panic;
use Domains\Driver\Resources\PanicResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $panics = QueryBuilder::for(subject: Panic::class)
            ->whereHas('journey.train', function ($query): void {
                $query->where('driver_id', Auth::id());
            })
            ->with(relations: [
                'journey.train.line',
                'journey.train.origin',
                'journey.train.destination',
                'journey.train.locomotiveNumber',
                'journey.train.driver',
                'journey.licenses',
                'shift',
            ])
            ->allowedFilters(filters: [
                AllowedFilter::exact(name: 'is_acknowledge'),
            ])
            ->get();

        return response(
            content: [
                'message' => 'Panics fetched successfully.',
                'panics' => PanicResource::collection(
                    resource: $panics,
                ),
            ],
            status: Http::OK(),
        );
    }
}
