<?php

declare(strict_types=1);

namespace App\Http\Controllers\Constants;

use Domains\Shared\Models\Desk;
use Domains\Shared\Resources\DeskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class DeskController
{
    public function index(Request $request): Response
    {
        $desks  = QueryBuilder::for(subject: Desk::class)
            ->get();

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

    public function show(Desk $desk): Response
    {
        return response(
            content: [
                'message' => 'Desk fetched successfully.',
                'desk' => new DeskResource(
                    resource: $desk,
                ),
            ],
            status: Http::OK(),
        );
    }
}
