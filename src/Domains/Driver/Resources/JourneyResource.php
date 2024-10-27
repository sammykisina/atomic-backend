<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\Journey;
use Domains\Operator\Resources\LicenseResource;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Journey $resource */
final class JourneyResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'is_active' => $this->resource->is_active,
            'is_authorized' => $this->resource->is_authorized,
            'train' => new TrainResource(
                resource: $this->whenLoaded(
                    relationship: 'train',
                ),
            ),
            'licenses' => LicenseResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'licenses',
                ),
            ),
            'created_at' => $this->resource->created_at,
        ];
    }
}
