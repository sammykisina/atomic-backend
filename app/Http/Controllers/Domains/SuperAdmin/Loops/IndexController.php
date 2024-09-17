<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Loops;

use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Resources\LoopResource;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request)
    {
        $loops  = QueryBuilder::for(subject: Loop::class)
            ->get();

        return response(
            content: [
                'message' => 'Loops fetched successfully.',
                'loops' => LoopResource::collection(
                    resource: $loops,
                ),
            ],
            status: Http::OK(),
        );
    }
}
