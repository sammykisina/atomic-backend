<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\LocomotiveNumbers;

use Domains\SuperAdmin\Models\LocomotiveNumber;
use Domains\SuperAdmin\Resources\LocomotiveNumberResource;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(): Response
    {
        $locomotive_numbers  = QueryBuilder::for(LocomotiveNumber::class)
            ->get();

        return response(
            content: [
                'message' => 'Locomotive numbers fetched successfully.',
                'locomotive_numbers' => LocomotiveNumberResource::collection(
                    resource: $locomotive_numbers,
                ),
            ],
            status: Http::OK(),
        );
    }
}
