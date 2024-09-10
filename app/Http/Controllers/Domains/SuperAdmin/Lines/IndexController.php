<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Lines;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Resources\LineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $lines  = QueryBuilder::for(Line::class)
            ->allowedIncludes('regions', 'counties', 'stations.loops')
            ->get();

        return response(
            content: [
                'message' => 'Lines fetched successfully.',
                'lines' => LineResource::collection(
                    resource: $lines,
                ),
            ],
            status: Http::OK(),
        );
    }
}
