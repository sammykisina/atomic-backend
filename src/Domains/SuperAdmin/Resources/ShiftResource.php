<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Shift $resource */
final class ShiftResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'desk' => new DeskResource(
                resource: $this->whenLoaded(
                    relationship: 'desk',
                ),
            ),
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'start_station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'startStation',
                ),
            ),
            'end_station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'endStation',
                ),
            ),
            'day' => $this->resource->day,
            'time' => [
                'from' => $this->resource->from,
                'to' => $this->resource->to,
            ],
            'status' => $this->resource->status,
            'active' => $this->resource->active,
            'operator' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'user',
                ),
            ),
        ];
    }
}
