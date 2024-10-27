<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Panic;

use Domains\Driver\Models\Panic;
use Domains\Driver\Resources\PanicResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ActivePanicController
{
    public function __invoke(Request $request): Response
    {

        $panic = QueryBuilder::for(subject: Panic::class)
            ->whereHas('journey.train', function ($query): void {
                $query->where('driver_id', Auth::id());
            })
            ->where('is_acknowledge', false)
            ->with(relations: [
                'journey.train.line',
                'journey.train.origin',
                'journey.train.destination',
                'journey.train.locomotiveNumber',
                'journey.train.driver',
                'journey.licenses',
                'shift',
            ])
            ->first();

        return response(
            content: [
                'message' => 'Active panic fetched successfully.',
                'panics' => $panic ? new PanicResource(
                    resource: $panic,
                ) : null,
            ],
            status: Http::OK(),
        );
    }
}
