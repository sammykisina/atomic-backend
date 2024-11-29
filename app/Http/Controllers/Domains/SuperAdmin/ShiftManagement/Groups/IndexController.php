<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\ShiftManagement\Groups;

use Domains\SuperAdmin\Models\Group;
use Domains\SuperAdmin\Resources\ShiftManagement\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $groups = QueryBuilder::for(subject: Group::class)
            ->with(relations: [
                // 'desks',
            ])
            ->get();

        return response(
            content: [
                'message' => 'Groups fetched successfully.',
                'groups' => GroupResource::collection(
                    resource: $groups,
                ),
            ],
            status: Http::OK(),
        );
    }
}
