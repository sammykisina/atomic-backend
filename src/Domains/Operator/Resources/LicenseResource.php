<?php

declare(strict_types=1);

namespace Domains\Operator\Resources;

use Domains\Driver\Models\License;
use Domains\Shared\Resources\DateResource;
use Domains\Shared\Resources\UserResource;
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
            'origin' => $this->whenLoaded(
                relationship: 'originable',
            ),
            'train_at_origin' => $this->resource->train_at_origin,
            'through' => $this->resource->through,
            'destination' => $this->whenLoaded(
                relationship: 'destinationable',
            ),
            'train_at_destination' => $this->resource->train_at_destination,

        ];
    }
}
