<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Station $resource */
final class StationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'kilometer' => [
                'start' => $this->resource->start_kilometer,
                'end' => $this->resource->end_kilometer,
            ],
            'start' => [
                'latitude' => $this->resource->start_latitude,
                'longitude' => $this->resource->start_longitude,
            ],
            'end' => [
                'latitude' => $this->resource->end_latitude,
                'longitude' => $this->resource->end_longitude,
            ],
            'is_yard' => $this->resource->is_yard,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'position_from_line' => $this->resource->position_from_line,
            'loops' =>  LoopResource::collection(
                $this->whenLoaded(
                    relationship:'loops',
                ),
            ),
        ];
    }
}
