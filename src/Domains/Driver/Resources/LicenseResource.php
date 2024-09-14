<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\License;
use Domains\Shared\Resources\DateResource;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LineResource;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  License $resource */
final class LicenseResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'to_station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ),
            'section' => new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ),
            'to_station_main_line' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'main',
                ),
            ),
            'to_station_loop' => new LoopResource(
                resource: $this->whenLoaded(
                    relationship:'loop',
                ),
            ),
            'driver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship:'loop',
                ),
            ),
            'status' => $this->resource->status,
            'direction' => $this->resource->direction,
            'issuer' => new UserResource(
                resource: $this->whenLoaded(
                    relationship:'issuer',
                ),
            ),
            'rejector' => new UserResource(
                resource: $this->whenLoaded(
                    relationship:'rejector',
                ),
            ),
            'issued_at' => new DateResource(
                resource: $this->resource->issued_at,
            ),
            'rejected_at' => new DateResource(
                resource: $this->resource->expires_at,
            ),
            'confirmation_at' => new DateResource(
                resource: $this->resource->confirmation_at,
            ),
        ];
    }
}
