<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Setup\Desks;

use Domains\SuperAdmin\Models\Desk;
use Domains\SuperAdmin\Resources\DeskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $desks = QueryBuilder::for(subject: Desk::class)->get();

        return response(
            content: [
                'message' => 'Desks fetched successfully.',
                'desks' => DeskResource::collection(
                    resource: $desks,
                ),
            ],
            status: Http::OK(),
        );
    }
}
