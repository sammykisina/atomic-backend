<?php

declare(strict_types=1);

namespace Domains\PrincipleEngineer\Resources;

use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LineResource;
use Domains\SuperAdmin\Resources\RegionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read UserRegion resource */
final class UserRegionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'staff' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'user',
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
            'region' => new RegionResource(
                resource: $this->whenLoaded(
                    relationship: 'region',
                ),
            ),

            'start_kilometer' => $this->resource->start_kilometer,
            'end_kilometer' => $this->resource->end_kilometer,
            'owner' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'owner',
                ),
            ),
        ];
    }
}
