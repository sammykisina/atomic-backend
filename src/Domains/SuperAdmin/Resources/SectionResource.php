<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Driver\Enums\AreaTypes;
use Domains\SeniorEngineer\Resources\SpeedResource;
use Domains\SuperAdmin\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Section $resource */
final class SectionResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'line_id' => $this->resource->line_id,
            'station_id' => $this->resource->station_id,
            'name' => [
                'start' => $this->resource->start_name,
                'end' => $this->resource->end_name,
            ],
            'fullname' => $this->resource->fullname,
            'kilometers' => [
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

            'number_of_kilometers_to_divide_section_to_subsection' => $this->resource->number_of_kilometers_to_divide_section_to_subsection,

            'station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ),
            'status' => $this->resource->status,
            'speed' => $this->resource->speed,
            'type' =>  AreaTypes::SECTION->value,
            'section_type' => $this->resource->section_type,
            'speed_restrictions' => SpeedResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'speeds',
                ),
            ),
            'geofence_name' => $this->geofence_name,
        ];
    }
}
