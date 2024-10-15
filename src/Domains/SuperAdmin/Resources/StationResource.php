<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Driver\Enums\AreaTypes;
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
            'line_id' => $this->resource->line_id,
            'kilometer' => [
                'start' => $this->resource->start_kilometer,
                'end' => $this->resource->end_kilometer,
            ],

            'start_latitude_top' => $this->resource->start_latitude_top,
            'start_longitude_top' => $this->resource->start_longitude_top,
            'start_latitude_bottom' => $this->resource->start_latitude_bottom,
            'start_longitude_bottom' => $this->resource->start_longitude_bottom,
            'end_latitude_top' => $this->resource->end_latitude_top,
            'end_longitude_top' => $this->resource->end_longitude_top,
            'end_latitude_bottom' => $this->resource->end_latitude_bottom,
            'end_longitude_bottom' => $this->resource->end_longitude_bottom,

            'is_yard' => $this->resource->is_yard,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'position_from_line' => $this->resource->position_from_line,
            'loops' =>  LoopResource::collection(
                $this->whenLoaded(
                    relationship: 'loops',
                ),
            ),
            'starting_section' => new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ),
            'status' => $this->resource->status,
            'speed' => $this->resource->speed,
            'type' =>  AreaTypes::STATION->value,
        ];
    }
}
