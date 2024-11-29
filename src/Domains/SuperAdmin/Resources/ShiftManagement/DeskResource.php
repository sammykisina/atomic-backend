<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources\ShiftManagement;

use Domains\Shared\Resources\DateResource;
use Domains\SuperAdmin\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Desk $resource */
final class DeskResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'group' => new GroupResource(
                resource: $this->whenLoaded(
                    relationship: 'group',
                ),
            ),
            'created_at' => new DateResource(
                resource: $this->resource->created_at,
            ),
        ];
    }
}
