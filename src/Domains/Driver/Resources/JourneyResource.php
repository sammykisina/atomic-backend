<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\Journey;
use Domains\Operator\Resources\LicenseResource;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LocomotiveNumberResource;
use Domains\SuperAdmin\Resources\StationResource;
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
            'train_information' => [
                'train_number' => $this->resource->train,
                'service_order' => $this->resource->service_order,
                'number_of_wagons' => $this->resource->number_of_wagons,
                'locomotive_number' => new LocomotiveNumberResource(
                    $this->whenLoaded(
                        relationship: 'locomotiveNumber',
                    )
                )
            ],
            'origin' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'origin',
                ),
            ),
            'destination' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'destination',
                ),
            ),
            'status' => $this->resource->status,
            'driver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'driver',
                ),
            ),
            'licenses' => LicenseResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'licenses',
                ),
            ),
            
        ];
    }
}
