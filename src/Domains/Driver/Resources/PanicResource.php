<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\Panic;
use Domains\SuperAdmin\Resources\ShiftManagement\ShiftResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Panic $resource */
final class PanicResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'journey' => new JourneyResource(
                resource: $this->whenLoaded(
                    relationship: 'journey',
                ),
            ),
            'shift' => new ShiftResource(
                resource: $this->whenLoaded(
                    relationship: 'shift',
                ),
            ),
            'issue' => $this->resource->issue,
            'description' => $this->resource->description,
            'time' => $this->resource->time,
            'date' => $this->resource->date,
            'latitude' => $this->resource->latitude,
            'longitude' => $this->resource->longitude,
            'is_acknowledge' => $this->resource->is_acknowledge,
            'time_of_acknowledge' => $this->resource->time_of_acknowledge,
            'date_of_acknowledge' => $this->resource->date_of_acknowledge,
        ];
    }
}
