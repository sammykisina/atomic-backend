<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\Location;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Location $resource */
final class LocationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'coordinates' => [
                'latitude' => $this->resource->latitude,
                'longitude' => $this->resource->longitude,
            ],
            'status' => $this->resource->status,
            'loop' => new LoopResource(
                resource: $this->whenLoaded(
                    relationship: 'loop',
                ),
            ),
            'main_line' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ),
            'section' => new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ),
        ];
    }
}
