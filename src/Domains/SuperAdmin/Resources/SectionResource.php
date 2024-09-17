<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

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
            'name' => [
                'start' => $this->resource->start_name,
                'end' => $this->resource->end_name,
            ],
            'kilometers' => [
                'start' => $this->resource->start_kilometer,
                'end' => $this->resource->end_kilometer,
            ],
            // 'start' => [
            //     'latitude' => $this->resource->start_latitude,
            //     'longitude' => $this->resource->start_longitude,
            // ],
            // 'end' => [
            //     'latitude' => $this->resource->end_latitude,
            //     'longitude' => $this->resource->end_longitude,
            // ],

            'start_latitude_top' => $this->resource->start_latitude_top,
            'start_longitude_top' => $this->resource->start_longitude_top,
            'start_latitude_bottom' => $this->resource->start_latitude_bottom,
            'start_longitude_bottom' => $this->resource->start_longitude_bottom,
            'end_latitude_top' => $this->resource->end_latitude_top,
            'end_longitude_top' => $this->resource->end_longitude_top,
            'end_latitude_bottom' => $this->resource->end_latitude_bottom,
            'end_longitude_bottom' => $this->resource->end_longitude_bottom,

            'number_of_kilometers_to_divide_section_to_subsection' => $this->resource->number_of_kilometers_to_divide_section_to_subsection,

            'station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ),
        ];
    }
}
