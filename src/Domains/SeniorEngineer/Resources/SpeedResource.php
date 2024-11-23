<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Resources;

use Domains\SeniorEngineer\Models\Speed;
use Domains\SuperAdmin\Resources\LineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Speed $resource */
final class SpeedResource extends JsonResource
{
    /**
     * TRANSFORM THE RESOURCE INTO AN ARRAY
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'area' => $this->whenLoaded(
                relationship: 'areable',
            ),
            'speed' => $this->resource->speed,
            'start_kilometer' => $this->resource->start_kilometer,
            'end_kilometer' => $this->resource->end_kilometer,
            'start_kilometer_latitude' => $this->resource->start_kilometer_latitude,
            'start_kilometer_longitude' => $this->resource->start_kilometer_longitude,
            'end_kilometer_latitude' => $this->resource->end_kilometer_latitude,
            'end_kilometer_longitude' => $this->resource->end_kilometer_longitude,
        ];
    }
}
