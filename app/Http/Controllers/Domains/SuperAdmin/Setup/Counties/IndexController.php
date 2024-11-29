<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Setup\Counties;

use Domains\SuperAdmin\Models\County;
use Domains\SuperAdmin\Resources\CountyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $counties = QueryBuilder::for(County::class)->get();

        return response(
            content: [
                'message' => 'Counties fetched successfully.',
                'counties' => CountyResource::collection(
                    resource: $counties,
                ),
            ],
            status: Http::OK(),
        );
    }
}
