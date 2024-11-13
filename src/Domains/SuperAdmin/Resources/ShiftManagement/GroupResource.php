<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources\ShiftManagement;

use Domains\SuperAdmin\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Group $resource */
final class GroupResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'lines' => $this->resource->lines,
            'stations' => $this->resource->stations,
        ];
    }
}
