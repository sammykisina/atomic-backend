<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LoopResource extends JsonResource
{
    /** @return array<string, mixed>  */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'distance' => $this->resource->distance,
            'position' => $this->resource->position,
            'station' => StationResource::make(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ),
        ];
    }
}
