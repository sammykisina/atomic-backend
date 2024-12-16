<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Models\LocomotiveNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  LocomotiveNumber $resource */
final class LocomotiveNumberResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'obc_id' => $this->resource->obc_id,
            'driver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'driver',
                ),
            ),
            'obc' => new ObcResource(
                resource: $this->whenLoaded(
                    relationship: 'obc',
                ),
            ),
        ];
    }
}
