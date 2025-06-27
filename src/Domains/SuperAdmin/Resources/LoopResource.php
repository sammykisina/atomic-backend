<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Driver\Enums\AreaTypes;
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
            'start_latitude_top' => $this->resource->start_latitude_top,
            'start_longitude_top' => $this->resource->start_longitude_top,
            'start_latitude_bottom' => $this->resource->start_latitude_bottom,
            'start_longitude_bottom' => $this->resource->start_longitude_bottom,
            'end_latitude_top' => $this->resource->end_latitude_top,
            'end_longitude_top' => $this->resource->end_longitude_top,
            'end_latitude_bottom' => $this->resource->end_latitude_bottom,
            'end_longitude_bottom' => $this->resource->end_longitude_bottom,

            'status' => $this->resource->status,
            'type' =>  AreaTypes::LOOP->value,
            'geofence_name' => $this->geofence_name,
        ];
    }
}
