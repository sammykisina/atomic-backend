<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Shifts;

use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Resources\ShiftResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $shifts  = QueryBuilder::for(subject: Shift::class)
            ->allowedIncludes('user', 'startStation', 'endStation')
            ->get();

        return response(
            content: [
                'message' => 'Shifts fetched successfully.',
                'shifts' => ShiftResource::collection(
                    resource: $shifts,
                ),
            ],
            status: Http::OK(),
        );
    }
}
