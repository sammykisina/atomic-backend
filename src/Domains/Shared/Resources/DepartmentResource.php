<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Department $resource */

final class DepartmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'dates' => new DateResource(
                resource: $this->resource->created_at,
            ),
        ];
    }
}
