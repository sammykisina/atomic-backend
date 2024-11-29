<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Sections;

use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Resources\SectionResource;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request)
    {
        $sections  = QueryBuilder::for(Section::class)
            ->allowedIncludes('station')
            ->get();

        return response(
            content: [
                'message' => 'Sections fetched successfully.',
                'sections' => SectionResource::collection(
                    resource: $sections,
                ),
            ],
            status: Http::OK(),
        );
    }
}
