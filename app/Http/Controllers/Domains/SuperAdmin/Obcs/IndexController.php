<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Obcs;

use Domains\SuperAdmin\Models\Obc;
use Domains\SuperAdmin\Resources\ObcResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $obcs  = QueryBuilder::for(subject: Obc::class)
            ->get();

        return response(
            content: [
                'message' => 'Obcs fetched successfully.',
                'obcs' => ObcResource::collection(
                    resource: $obcs,
                ),
            ],
            status: Http::OK(),
        );
    }
}
