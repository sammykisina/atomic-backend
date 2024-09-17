<?php

declare(strict_types=1);

namespace Domains\Operator\Resources;

use Domains\Driver\Models\License;
use Domains\Shared\Resources\DateResource;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read License $resource */
final class LicenseResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'license_number' => $this->resource->license_number,
            'status' => $this->resource->status,
            'origin_station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'originStation',
                ),
            ),
            'destination_station' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'destinationStation',
                ),
            ),
            'main' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'main',
                ),
            ),
            'loop' => new LoopResource(
                resource: $this->whenLoaded(
                    relationship: 'loop',
                ),
            ),
            'though_section' => new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ),
            'direction' => $this->resource->direction,
            'issuer' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'issuer',
                ),
            ),
            'rejector' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'rejector',
                ),
            ),
            'issued_at' => new DateResource(
                resource: $this->resource->issued_at,
            ),
            'rejected_at' => new DateResource(
                resource: $this->resource->rejected_at,
            ),
            'confirmed_at' => new DateResource(
                resource: $this->resource->confirmed_at,
            ),

        ];
    }
}
