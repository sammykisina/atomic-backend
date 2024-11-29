<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\SuperAdmin\Models\County;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read County $resource */
final class CountyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
