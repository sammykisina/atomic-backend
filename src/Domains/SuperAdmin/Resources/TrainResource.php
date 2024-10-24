<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Train $resource */
final class TrainResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->name,
            'service_order' => $this->resource->service_order,
            'locomotive_number' => new LocomotiveNumberResource(
                resource: $this->whenLoaded(
                    relationship: 'locomotiveNumber',
                ),
            ),
            'tail_number' => $this->resource->tail_number,
            'number_of_wagons' => $this->resource->number_of_wagons,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'origin' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'origin',
                ),
            ),
            'destination' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'destination',
                ),
            ),
            'driver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'driver',
                ),
            ),
        ];
    }
}
