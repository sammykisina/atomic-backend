<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\Shared\Resources\RegionResource;
use Domains\SuperAdmin\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Line $resource */
final class LineResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'regions' => RegionResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'regions',
                ),
            ),
            'counties' => CountyResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'counties',
                ),
            ),
            'stations' => StationResource::collection(
                resource: $this->whenLoaded(
                    relationship:'stations',
                ),
            ),
        ];
    }
}
