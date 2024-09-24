<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Resources;

use Domains\SuperAdmin\Models\Region;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Region $resource */
final class RegionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'start_station' =>  isset($this->resource->pivot->start_station_id)
                ? new StationResource(Station::find($this->resource->pivot->start_station_id))
                : null, // Return null if pivot or start_station_id doesn't exist
            'end_station' =>  isset($this->resource->pivot->end_station_id)
                ? new StationResource(Station::find($this->resource->pivot->end_station_id))
                : null, // Return null if pivot or end_station_id doesn't exist
        ];

    }
}
